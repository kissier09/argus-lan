<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // Authenticate the registered client PC.
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'message' => 'Device token required.',
            ], 401);
        }

        $tokenHash = hash('sha256', trim($plainToken));

        $device = Device::where('api_token_hash', $tokenHash)->first();

        if (!$device) {
            return response()->json([
                'message' => 'Invalid device token.',
            ], 401);
        }

        // Validate registration details.
        $validator = Validator::make($request->all(), [
            'hostname' => ['required', 'string', 'max:255'],

            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'strand' => ['required', 'string', 'max:100'],
            'year' => ['required', 'string', 'max:50'],
            'section' => ['required', 'string', 'max:100'],

            'student_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'student_number'),
            ],

            'photo_data' => [
                'required',
                'string',
                'max:3000000',
                'regex:/^data:image\/jpeg;base64,[A-Za-z0-9+\/]+={0,2}$/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Registration validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Verify that the request comes from the registered PC.
        if ($data['hostname'] !== $device->hostname) {
            return response()->json([
                'message' => 'Hostname does not match registered device.',
            ], 403);
        }

        // Decode and validate the captured JPEG photo.
        $imageData = substr(
            $data['photo_data'],
            strlen('data:image/jpeg;base64,')
        );

        $imageBinary = base64_decode($imageData, true);

        if ($imageBinary === false) {
            return response()->json([
                'message' => 'Invalid captured photo.',
            ], 422);
        }

        $imageInfo = @getimagesizefromstring($imageBinary);

        if (
            $imageInfo === false ||
            ($imageInfo['mime'] ?? '') !== 'image/jpeg' ||
            strlen($imageBinary) > 2 * 1024 * 1024
        ) {
            return response()->json([
                'message' => 'The captured photo is invalid or exceeds 2 MB.',
            ], 422);
        }

        // Build the student's full name.
        $name = implode(' ', array_filter([
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
        ], fn ($part) => $part !== null && trim($part) !== ''));

        // Generate unique photo and QR identifiers.
        $photoPath = 'student-photos/' . Str::uuid() . '.jpg';
        $qrUuid = (string) Str::uuid();

        // Save the photo.
        if (!Storage::disk('public')->put($photoPath, $imageBinary)) {
            return response()->json([
                'message' => 'Unable to save student photo.',
            ], 500);
        }

        try {
            // Create the pending student account.
            $user = new User();

            $user->name = $name;
            $user->first_name = $data['first_name'];
            $user->middle_name = $data['middle_name'] ?? null;
            $user->last_name = $data['last_name'];

            $user->student_number = $data['student_number'];
            $user->strand = $data['strand'];
            $user->year = $data['year'];
            $user->section = $data['section'];

            $user->photo_path = $photoPath;
            $user->qr_code_uuid = $qrUuid;

            $user->registration_status = 'pending';
            $user->account_status = 'inactive';

            $user->save();
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($photoPath);
            throw $e;
        }

        return response()->json([
            'message' => 'Registration submitted successfully. Awaiting administrator approval.',
            'student' => [
                'id' => $user->id,
                'student_number' => $user->student_number,
                'name' => $user->name,
                'registration_status' => $user->registration_status,
                'account_status' => $user->account_status,
            ],
            'qr_code_uuid' => $user->qr_code_uuid,
        ], 201);
    }
}
