<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DeviceHeartbeatController extends Controller
{
    public function store(Request $request)
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'message' => 'Device token required.',
            ], 401);
        }

        $device = Device::whereNotNull('api_token_hash')
            ->get()
            ->first(function ($device) use ($plainToken) {
                return Hash::check($plainToken, $device->api_token_hash);
            });

        if (!$device) {
            return response()->json([
                'message' => 'Invalid device token.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'hostname' => ['required', 'string', 'max:255'],
            'ip_address' => ['nullable', 'ip'],
            'operating_system' => ['nullable', 'string', 'max:255'],
            'cpu_usage' => ['required', 'numeric', 'between:0,100'],
            'ram_usage' => ['required', 'numeric', 'between:0,100'],
            'battery_percentage' => ['sometimes', 'nullable', 'integer', 'between:0,100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid heartbeat data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($data['hostname'] !== $device->hostname) {
            return response()->json([
                'message' => 'Hostname does not match registered device.',
            ], 403);
        }

        $device->update([
            'ip_address' => $data['ip_address'] ?? $request->ip(),
            'operating_system' => $data['operating_system'] ?? $device->operating_system,
            'cpu_usage' => $data['cpu_usage'],
            'ram_usage' => $data['ram_usage'],
            'status' => 'online',
            'last_seen_at' => now(),
            'battery_percentage' => array_key_exists('battery_percentage', $data)
                ? $data['battery_percentage']
                : $device->battery_percentage,
        ]);

        return response()->json([
            'message' => 'Heartbeat received.',
            'last_seen_at' => $device->last_seen_at?->toISOString(),
        ]);
    }
}