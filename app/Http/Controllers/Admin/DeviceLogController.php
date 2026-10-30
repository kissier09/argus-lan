<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceLogController extends Controller
{
    public function index(Request $request, Device $device)
    {
        $search = trim($request->query('search', ''));

        $activities = ActivityLog::with('user')
            ->where('device_id', $device->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('activity_type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhere('result', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('strand', 'like', "%{$search}%")
                                ->orWhere('year_level', 'like', "%{$search}%")
                                ->orWhere('section', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.devices.logs', compact(
            'device',
            'activities',
            'search'
        ));
    }
}
