<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DeviceManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $status = strtolower($request->query('status', 'all'));

        if (!in_array($status, ['all', 'online', 'offline'], true)) {
            $status = 'all';
        }

        // A device is online only if it sent a heartbeat
        // within the last 10 seconds.
        // Device status represents student authentication.

        $devices = Device::query()
            ->when($status === 'online', function ($query) {
                $query->where('status', 'online');
            })
            ->when($status === 'offline', function ($query) {
                $query->where(function ($q) {
                    $q->where('status', 'offline')
                        ->orWhereNull('status');
                });
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('hostname', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhere('mac_address', 'like', "%{$search}%")
                        ->orWhere('operating_system', 'like', "%{$search}%");
                });
            })
            ->orderBy('hostname')
            ->paginate(9)
            ->withQueryString();

        // Calculate displayed status for every device on this page.
        $totalDevices = Device::count();

        $onlineDevices = Device::where('status', 'online')->count();

        $offlineDevices = $totalDevices - $onlineDevices;

        return view('admin.devices.index', compact(
            'devices',
            'search',
            'status',
            'totalDevices',
            'onlineDevices',
            'offlineDevices'
        ));
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()
            ->route('admin.devices.index')
            ->with('success', 'Device deleted successfully. Historical activity logs have been preserved.');
    }
}
