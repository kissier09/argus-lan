<?php
namespace App\Http\Controllers;

use App\Models\Device;

class DashboardController extends Controller
{
    public function index()
    {
        $devices = Device::orderBy('hostname')->get();

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
