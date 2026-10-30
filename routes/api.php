
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceHeartbeatController;

Route::post('/device/heartbeat', [DeviceHeartbeatController::class, 'store']);
