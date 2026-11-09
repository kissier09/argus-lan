<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceHeartbeatController;
use App\Http\Controllers\Api\StudentAuthenticationController;
use App\Http\Controllers\Api\StudentRegistrationController;

Route::post('/device/heartbeat', [DeviceHeartbeatController::class, 'store']);

Route::post('/student/identify', [StudentAuthenticationController::class, 'identify']);

Route::post('/student/verify-face', [StudentAuthenticationController::class, 'verifyFace']);

Route::post('/student/heartbeat', [StudentAuthenticationController::class, 'heartbeat']);

Route::post('/student/logout', [StudentAuthenticationController::class, 'logout']);

Route::post('/student/register', [StudentRegistrationController::class, 'store']);