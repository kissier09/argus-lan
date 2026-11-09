<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Device;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\ActivityLog;

class ExpireDisconnectedStudentSessions extends Command
{
    protected $signature = 'app:expire-disconnected-student-sessions';

    protected $description = 'Automatically log out students when their PC heartbeat is lost';

    public function handle()
    {
        // A device is considered disconnected after 30 seconds.
        $timeout = now()->subSeconds(30);

        $devices = Device::where('status', 'online')
            ->where(function ($query) use ($timeout) {
                $query->whereNull('last_seen_at')
                    ->orWhere('last_seen_at', '<=', $timeout);
            })
            ->get();

        foreach ($devices as $device) {
            DB::transaction(function () use ($device) {

                $lockedDevice = Device::where('id', $device->id)
                    ->lockForUpdate()
                    ->first();

                if (!$lockedDevice || $lockedDevice->status !== 'online') {
                    return;
                }

                // Recheck heartbeat in case the PC reconnected.
                if (
                    $lockedDevice->last_seen_at &&
                    $lockedDevice->last_seen_at->gt(now()->subSeconds(30))
                ) {
                    return;
                }

                $sessions = StudentSession::where('device_id', $lockedDevice->id)
                    ->lockForUpdate()
                    ->get();

                foreach ($sessions as $session) {
                    $student = User::where('id', $session->user_id)
                        ->lockForUpdate()
                        ->first();

                    // Invalidate the session token.
                    $session->update([
                        'session_token_hash' => hash('sha256', Str::random(64)),
                        'expires_at' => now(),
                    ]);

                    if ($student) {
                        $student->update([
                            'account_status' => 'inactive',
                        ]);

                        ActivityLog::create([
                            'user_id' => $student->id,
                            'device_id' => $lockedDevice->id,
                            'activity_type' => 'student_session_expired',
                            'description' => 'Student automatically logged out because the PC heartbeat was lost.',
                            'ip_address' => $lockedDevice->ip_address,
                            'result' => 'success',
                        ]);
                    }
                }

                // Mark the disconnected PC offline.
                $lockedDevice->update([
                    'status' => 'offline',
                ]);

                $this->info(
                    'Device ' . $lockedDevice->hostname . ' marked offline.'
                );
            }, 3);
        }

        return self::SUCCESS;
    }
}
