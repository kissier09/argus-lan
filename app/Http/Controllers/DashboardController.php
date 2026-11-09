<?php

namespace App\Http\Controllers;

use App\Models\Device;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::orderBy('hostname')->get();

        $cutoff = now()->subSeconds(10);

        foreach ($devices as $device) {
            $device->status = (
                $device->last_seen_at !== null &&
                $device->last_seen_at->gt($now->copy()->subSeconds(10))
            ) ? 'online' : 'offline';
        }

        $totalDevices = $devices->count();
        $onlineDevices = $devices->where('status', 'online')->count();
        $offlineDevices = $totalDevices - $onlineDevices;

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'onlineDevices',
            'offlineDevices'
        ));
    }
}
