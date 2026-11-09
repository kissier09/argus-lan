<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Models\StudentSession;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentAuthenticationController extends Controller
{
    public function identify(Request $request)
    {
        // Authenticate the registered device.
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'message' => 'Device token required.',
            ], 401);
        }

        $tokenHash = hash('sha256', trim($plainToken));

        $device = Device::where(
            'api_token_hash',
            $tokenHash
        )->first();

        if (!$device) {
            return response()->json([
                'message' => 'Invalid device token.',
            ], 401);
        }

        // Validate QR scan information.
        $validator = Validator::make($request->all(), [
            'hostname' => ['required', 'string', 'max:255'],
            'qr_code_uuid' => ['required', 'uuid'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid identification data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Confirm the requesting PC.
        if ($data['hostname'] !== $device->hostname) {
            return response()->json([
                'message' => 'Hostname does not match registered device.',
            ], 403);
        }

        // Find the student using the scanned QR code.
        $student = User::where(
            'qr_code_uuid',
            $data['qr_code_uuid']
        )->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student not found.',
            ], 404);
        }

        // Check approval before proceeding.
        if ($student->registration_status !== 'approved') {
            return response()->json([
                'message' => 'Student is not approved for login.',
            ], 403);
        }

        // Confirm a facial reference photo exists.
        if (
            empty($student->photo_path) ||
            !Storage::disk('public')->exists($student->photo_path)
        ) {
            return response()->json([
                'message' => 'Student facial reference photo is missing.',
            ], 403);
        }

        return response()->json([
            'message' => 'QR identification successful. Proceed to facial verification.',
            'next_step' => 'face_verification',
            'student' => [
                'id' => $student->id,
                'student_number' => $student->student_number,
                'name' => $student->name,
                'strand' => $student->strand,
                'year' => $student->year,
                'section' => $student->section,
            ],
        ]);
    }
        public function verifyFace(Request $request)
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'message' => 'Device token required.',
            ], 401);
        }

        $device = Device::where(
            'api_token_hash',
            hash('sha256', trim($plainToken))
        )->first();

        if (!$device) {
            return response()->json([
                'message' => 'Invalid device token.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'hostname' => ['required', 'string', 'max:255'],
            'qr_code_uuid' => ['required', 'uuid'],
            'frames' => ['required', 'array', 'size:10'],
            'frames.*' => ['required', 'file', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid face verification data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($data['hostname'] !== $device->hostname) {
            return response()->json([
                'message' => 'Hostname does not match registered device.',
            ], 403);
        }

        $student = User::where(
            'qr_code_uuid',
            $data['qr_code_uuid']
        )->first();

        if (!$student || $student->registration_status !== 'approved') {
            return response()->json([
                'message' => 'Student is not approved for login.',
            ], 403);
        }

        if (
            empty($student->photo_path) ||
            !Storage::disk('public')->exists($student->photo_path)
        ) {
            return response()->json([
                'message' => 'Student facial reference photo is missing.',
            ], 403);
        }

        try {
            $referencePath = Storage::disk('public')->path(
                $student->photo_path
            );

            $response = Http::withToken(
                config('services.face_verification.token')
            )
            ->timeout(60)
            ->attach(
                'reference',
                file_get_contents($referencePath),
                basename($referencePath)
            );

            foreach ($data['frames'] as $index => $frame) {
                $response = $response->attach(
                    'frames',
                    file_get_contents($frame->getRealPath()),
                    'frame_' . ($index + 1) . '.jpg'
                );
            }

            $response = $response->post(
                rtrim(config('services.face_verification.url'), '/') . '/verify'
            );

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Face verification service failed.',
                    'service_status' => $response->status(),
                    'service_error' => $response->json() ?? $response->body(),
                ], 502);
            }

            // Do not create a session if facial verification fails.
            if ($response->json('verified') !== true) {
                return response()->json([
                    'message' => 'Face verification failed.',
                    'verified' => false,
                    'median_similarity' => $response->json('median_similarity'),
                    'matching_frames' => $response->json('matching_frames'),
                    'total_frames' => $response->json('total_frames'),
                ], 403);
            }

            // Create the student session atomically.
            $result = DB::transaction(function () use ($student, $device, $request) {

                // Lock the student record to prevent simultaneous logins.
                $lockedStudent = User::where('id', $student->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $session = StudentSession::where('user_id', $lockedStudent->id)
                    ->lockForUpdate()
                    ->first();

                // Reject login if another active session exists.
                if (
                    $session &&
                    $session->expires_at &&
                    $session->expires_at->isFuture()
                ) {
                    ActivityLog::create([
                        'user_id' => $lockedStudent->id,
                        'device_id' => $device->id,
                        'activity_type' => 'login_rejected',
                        'description' => 'Student already has an active PC session.',
                        'ip_address' => $request->ip(),
                        'result' => 'failed',
                    ]);

                    return ['already_active' => true];
                }

                // Generate a new random session token.
                $plainSessionToken = Str::random(64);

                $sessionData = [
                    'device_id' => $device->id,
                    'session_token_hash' => hash('sha256', $plainSessionToken),
                    'last_heartbeat_at' => now(),
                    'expires_at' => now()->addSeconds(30),
                ];

                if ($session) {
                    $session->update($sessionData);
                } else {
                    $session = StudentSession::create(array_merge(
                        ['user_id' => $lockedStudent->id],
                        $sessionData
                    ));
                }

                $lockedStudent->update([
                    'account_status' => 'active',
                ]);
                $device->update([
                    'status' => 'online',
                ]);

                ActivityLog::create([
                    'user_id' => $lockedStudent->id,
                    'device_id' => $device->id,
                    'activity_type' => 'student_login',
                    'description' => 'Student successfully logged in through QR and face verification.',
                    'ip_address' => $request->ip(),
                    'result' => 'success',
                ]);

                return [
                    'already_active' => false,
                    'session_token' => $plainSessionToken,
                    'expires_at' => $session->expires_at,
                ];
            }, 3);

            if ($result['already_active']) {
                return response()->json([
                    'message' => 'This student already has an active session on another PC.',
                    'verified' => true,
                ], 409);
            }

            return response()->json([
                'message' => 'Student authentication successful.',
                'verified' => true,
                'session_token' => $result['session_token'],
                'expires_at' => $result['expires_at'],
                'median_similarity' => $response->json('median_similarity'),
                'matching_frames' => $response->json('matching_frames'),
                'total_frames' => $response->json('total_frames'),
                'student' => [
                    'id' => $student->id,
                    'student_number' => $student->student_number,
                    'name' => $student->name,
                    'strand' => $student->strand,
                    'year' => $student->year,
                    'section' => $student->section,
                ],
            ]);

        } catch (\Throwable $error) {
            report($error);

            return response()->json([
                'message' => 'Unable to complete face verification.',
            ], 500);
        }
    }

        public function heartbeat(Request $request)
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json(['message' => 'Device token required.'], 401);
        }

        $device = Device::where(
            'api_token_hash',
            hash('sha256', trim($plainToken))
        )->first();

        if (!$device) {
            return response()->json(['message' => 'Invalid device token.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'hostname' => ['required', 'string', 'max:255'],
            'session_token' => ['required', 'string', 'size:64'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid heartbeat data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($data['hostname'] !== $device->hostname) {
            return response()->json(['message' => 'Hostname mismatch.'], 403);
        }

        $session = StudentSession::where(
            'session_token_hash',
            hash('sha256', $data['session_token'])
        )
            ->where('device_id', $device->id)
            ->first();

        if (!$session || !$session->expires_at || !$session->expires_at->isFuture()) {
            return response()->json([
                'message' => 'Student session is invalid or expired.',
            ], 401);
        }

        $session->update([
            'last_heartbeat_at' => now(),
            'expires_at' => now()->addSeconds(30),
        ]);

        return response()->json([
            'message' => 'Student session heartbeat successful.',
            'expires_at' => $session->fresh()->expires_at,
        ]);
    }

    public function logout(Request $request)
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json(['message' => 'Device token required.'], 401);
        }

        $device = Device::where(
            'api_token_hash',
            hash('sha256', trim($plainToken))
        )->first();

        if (!$device) {
            return response()->json(['message' => 'Invalid device token.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'hostname' => ['required', 'string', 'max:255'],
            'session_token' => ['required', 'string', 'size:64'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid logout data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($data['hostname'] !== $device->hostname) {
            return response()->json(['message' => 'Hostname mismatch.'], 403);
        }

        $result = DB::transaction(function () use ($data, $device, $request) {
            $session = StudentSession::where(
                'session_token_hash',
                hash('sha256', $data['session_token'])
            )
                ->where('device_id', $device->id)
                ->lockForUpdate()
                ->first();

            if (!$session || !$session->expires_at || !$session->expires_at->isFuture()) {
                return false;
            }

            $student = User::where('id', $session->user_id)
                ->lockForUpdate()
                ->first();

            if (!$student) {
                return false;
            }

            $session->update([
                'session_token_hash' => hash('sha256', Str::random(64)),
                'last_heartbeat_at' => now(),
                'expires_at' => now(),
            ]);

            $student->update([
                'account_status' => 'inactive',
            ]);
            // Mark the PC as offline after student logout.
            $device->update([
                'status' => 'offline',
            ]);

            ActivityLog::create([
                'user_id' => $student->id,
                'device_id' => $device->id,
                'activity_type' => 'student_logout',
                'description' => 'Student successfully logged out.',
                'ip_address' => $request->ip(),
                'result' => 'success',
            ]);

            return true;
        }, 3);

        if (!$result) {
            return response()->json([
                'message' => 'Student session is invalid or expired.',
            ], 401);
        }

        return response()->json([
            'message' => 'Student logout successful.',
        ]);
    }
}
