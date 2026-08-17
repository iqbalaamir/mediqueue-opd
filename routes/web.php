<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\QueueDeskController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Patient\BookingController;
use App\Http\Controllers\Patient\BookingFeeController;
use App\Http\Controllers\Patient\BookingOtpController;
use App\Http\Controllers\Patient\PaymentController;
use App\Http\Controllers\Patient\QueueController;
use App\Http\Controllers\Patient\VerifyController;
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

Route::get('/appointments/{appointment}/queue', [QueueController::class, 'show'])
    ->name('queue.show');

Route::get('/appointments/{appointment}/queue/snapshot', [QueueController::class, 'snapshot'])
    ->name('queue.snapshot');

Route::get('/verify', [VerifyController::class, 'index'])->name('verify.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::middleware('admin')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('cities', CityController::class)->except(['show']);
        Route::resource('hospitals', HospitalController::class)->except(['show']);
        Route::resource('departments', DepartmentController::class)->except(['show']);
        Route::resource('doctors', DoctorController::class)->except(['show']);
        Route::resource('slots', SlotController::class)->except(['show']);
        Route::post('/slots/bulk', [SlotController::class, 'bulk'])
            ->middleware('throttle:admin_write')
            ->name('slots.bulk');

        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])
            ->middleware('throttle:admin_write')
            ->name('appointments.status');

        Route::get('/queues', [QueueDeskController::class, 'index'])->name('queues.index');
        Route::post('/queues/call-next', [QueueDeskController::class, 'callNext'])
            ->middleware('throttle:admin_write')
            ->name('queues.call-next');
        Route::post('/queues/{queueEntry}/serve', [QueueDeskController::class, 'serve'])
            ->middleware('throttle:admin_write')
            ->name('queues.serve');
        Route::post('/queues/{queueEntry}/complete', [QueueDeskController::class, 'complete'])
            ->middleware('throttle:admin_write')
            ->name('queues.complete');
        Route::post('/queues/{queueEntry}/skip', [QueueDeskController::class, 'skip'])
            ->middleware('throttle:admin_write')
            ->name('queues.skip');
        Route::post('/queues/{queueEntry}/recall', [QueueDeskController::class, 'recall'])
            ->middleware('throttle:admin_write')
            ->name('queues.recall');
        Route::post('/queues/doctor-delay', [QueueDeskController::class, 'doctorDelay'])
            ->middleware('throttle:admin_write')
            ->name('queues.doctor-delay');
        Route::post('/queues/doctor-status', [QueueDeskController::class, 'doctorStatus'])
            ->middleware('throttle:admin_write')
            ->name('queues.doctor-status');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::post('/notifications/{notification}/resend', [NotificationController::class, 'resend'])
            ->middleware('throttle:admin_write')
            ->name('notifications.resend');
        Route::post('/notifications/support', [NotificationController::class, 'support'])
            ->middleware('throttle:admin_write')
            ->name('notifications.support');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])
            ->middleware('throttle:admin_write')
            ->name('settings.update');
    });
});
