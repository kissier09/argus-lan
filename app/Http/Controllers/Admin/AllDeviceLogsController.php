<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AllDeviceLogsController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $activities = ActivityLog::with('device')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('activity_type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhere('result', 'like', "%{$search}%")
                        ->orWhereHas('device', function ($deviceQuery) use ($search) {
                            $deviceQuery->where('hostname', 'like', "%{$search}%")
                                ->orWhere('ip_address', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.devices.all-logs', compact(
            'activities',
            'search'
        ));
    }
}
