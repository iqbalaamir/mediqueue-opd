<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('book')->name('book.')->group(function () {
    Route::view('/', 'components.placeholder', [
        'title' => 'Book Appointment',
        'module' => '3',
        'message' => 'Patient booking wizard — city, hospital, doctor, slot, details, payment.',
    ])->name('index');
});

Route::view('/verify', 'components.placeholder', [
    'title' => 'Verify Appointment',
    'module' => '3',
    'message' => 'Lookup by appointment number or mobile number.',
])->name('verify.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/login', 'admin.login-placeholder')->name('login');

    Route::post('/logout', fn () => redirect()->route('admin.login', absolute: false))->name('logout');

    $adminPlaceholder = fn (string $title, string $module, string $message = '') => view('admin.placeholder', compact('title', 'module', 'message') + [
        'message' => $message ?: "Admin {$title} will be implemented in Module {$module}.",
    ]);

    Route::get('/dashboard', fn () => $adminPlaceholder('Dashboard', '8'))->name('dashboard');
    Route::get('/cities', fn () => $adminPlaceholder('Cities', '6'))->name('cities.index');
    Route::get('/hospitals', fn () => $adminPlaceholder('Hospitals', '6'))->name('hospitals.index');
    Route::get('/departments', fn () => $adminPlaceholder('Departments', '6'))->name('departments.index');
    Route::get('/doctors', fn () => $adminPlaceholder('Doctors', '6'))->name('doctors.index');
    Route::get('/slots', fn () => $adminPlaceholder('Slots', '7'))->name('slots.index');
    Route::get('/appointments', fn () => $adminPlaceholder('Appointments', '7'))->name('appointments.index');
    Route::get('/queues', fn () => $adminPlaceholder('Queue Desk', '4'))->name('queues.index');
    Route::get('/notifications', fn () => $adminPlaceholder('Notifications', '5'))->name('notifications.index');
    Route::get('/reports', fn () => $adminPlaceholder('Reports', '8'))->name('reports.index');
    Route::get('/settings', fn () => $adminPlaceholder('Settings', '8'))->name('settings.index');
});
