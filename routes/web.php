<?php

use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Patient\BookingController;
use App\Http\Controllers\Patient\BookingFeeController;
use App\Http\Controllers\Patient\BookingOtpController;
use App\Http\Controllers\Patient\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PromoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/promo', [PromoController::class, 'show'])->name('promo');

Route::prefix('book')->name('book.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/cities/{city}', [BookingController::class, 'hospitals'])->name('hospitals');
    Route::get('/hospitals/{hospital}', [BookingController::class, 'doctors'])->name('doctors');
    Route::get('/doctors/{doctor}', [BookingController::class, 'schedule'])->name('schedule');
    Route::get('/details', [BookingController::class, 'details'])->name('details');

    Route::post('/', [BookingController::class, 'store'])
        ->middleware('throttle:booking_store')
        ->name('store');

    Route::post('/fee-quote', [BookingFeeController::class, 'quote'])
        ->middleware('throttle:fee_quote')
        ->name('fee-quote');

    Route::post('/otp/send', [BookingOtpController::class, 'send'])
        ->middleware('throttle:otp_send')
        ->name('otp.send');

    Route::post('/otp/verify', [BookingOtpController::class, 'verify'])
        ->middleware('throttle:otp_verify')
        ->name('otp.verify');

    Route::get('/pay/{appointment}', [PaymentController::class, 'show'])->name('pay');

    Route::post('/pay/{appointment}/demo', [PaymentController::class, 'demoPay'])
        ->middleware('throttle:payment_demo')
        ->name('pay.demo');

    Route::post('/pay/{appointment}/fail', [PaymentController::class, 'demoFail'])
        ->middleware('throttle:payment_demo')
        ->name('pay.fail');
});

Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])
    ->name('appointments.show');

Route::view('/verify', 'components.placeholder', [
    'title' => 'Verify Appointment',
    'module' => '3',
    'message' => 'Lookup by appointment number or mobile number.',
])->name('verify.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/login', 'admin.login-placeholder')->name('login');

    Route::post('/logout', fn () => redirect()->to(route('admin.login', absolute: false)))->name('logout');

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
