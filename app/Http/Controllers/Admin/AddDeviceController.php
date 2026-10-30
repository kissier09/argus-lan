<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddDeviceController extends Controller
{
    public function create()
    {
        return view('admin.devices.create');
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostname' => ['required', 'string', 'max:255', 'unique:devices,hostname'],
            'ip_address' => ['nullable', 'ip', 'unique:devices,ip_address'],
            'operating_system' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['status'] = 'Offline';

        Device::create($validated);

        return redirect()
            ->route('admin.devices.index')
            ->with('success', 'Device registered successfully.');
    }

}
