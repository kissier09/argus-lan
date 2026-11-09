<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AddDeviceController extends Controller
{
    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostname' => [
                'required',
                'string',
                'max:255',
                'unique:devices,hostname',
            ],
            'ip_address' => [
                'nullable',
                'ip',
                'unique:devices,ip_address',
            ],
            'mac_address' => [
                'nullable',
                'string',
                'max:255',
                'unique:devices,mac_address',
            ],
            'operating_system' => [
                'nullable',
                'string',
                'max:255',
            ],
            'rustdesk_id' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $plainToken = Str::random(64);

        $validated['status'] = 'offline';
        $validated['api_token_hash'] = hash('sha256', $plainToken);

        $device = Device::create($validated);

        return redirect()
            ->route('admin.devices.create')
            ->with('success', 'Device registered successfully.')
            ->with('device_token', $plainToken)
            ->with('registered_hostname', $device->hostname);
    }
}
