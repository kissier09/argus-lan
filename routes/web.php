<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DeviceManagementController;
use App\Http\Controllers\Admin\DeviceLogController;
use App\Http\Controllers\Admin\AllDeviceLogsController;
use App\Http\Controllers\Admin\AddDeviceController;




Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'enrolledUsers' => User::where('registration_status', 'approved')->count(),
        'pendingUsers' => User::where('registration_status', 'pending')->count(),
        'totalComputers' => Device::count(),
    ]);
})->middleware('auth:admin')->name('dashboard');


Route::get('/admin/users', [UserManagementController::class, 'index'])
    ->middleware('auth:admin')
    ->name('admin.users.index');

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])
        ->name('admin.users.create');

    Route::post('/admin/users', [UserManagementController::class, 'store'])
        ->name('admin.users.store');
    Route::get('/admin/users/{user}/qr', [UserManagementController::class, 'showQr'])
        ->name('admin.users.qr');
    Route::get('/admin/pending-users', [
        UserManagementController::class,
        'pending'
    ])->name('admin.users.pending');

    Route::patch('/admin/users/{user}/approve', [
        UserManagementController::class,
        'approve'
    ])->name('admin.users.approve');

    Route::patch('/admin/users/{user}/reject', [
        UserManagementController::class,
        'reject'
    ])->name('admin.users.reject');

    Route::get(
        '/admin/users/{user}/notifications',
        [\App\Http\Controllers\Admin\StudentActivityController::class, 'index']
    )->name('admin.users.notifications');

    Route::get('/admin/devices', [DeviceManagementController::class, 'index'])
        ->name('admin.devices.index');

    Route::get('/admin/devices/{device}/logs', [DeviceLogController::class, 'index'])
        ->name('admin.devices.logs');

    Route::get('/admin/device-logs', [AllDeviceLogsController::class, 'index'])
        ->name('admin.devices.all-logs');

    Route::get('/admin/devices/create', [AddDeviceController::class, 'create'])
        ->name('admin.devices.create');

    Route::post('/admin/devices', [AddDeviceController::class, 'store'])
        ->name('admin.devices.store');

    Route::delete('/admin/devices/{device}', [DeviceManagementController::class, 'destroy'])
        ->name('admin.devices.destroy');

    Route::patch('/admin/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])
        ->name('admin.users.deactivate');

    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->name('admin.users.edit');

    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])
        ->name('admin.users.update');
});



Route::middleware('auth:admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
