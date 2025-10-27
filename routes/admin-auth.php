<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\UnifiedAttendanceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\Auth\OverdueBookController;
use App\Http\Controllers\Admin\TeachersVisitorsAttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('admin.auth.login');
    Route::post('login', [LoginController::class, 'store'])->name('admin.auth.login.submit');

    Route::get('register', [RegisteredUserController::class, 'create'])->name('admin.auth.register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('admin.auth.register.submit');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

        // Unified Attendance routes (Students + Teachers)
        Route::prefix('attendance')->name('admin.attendance.')->group(function () {
            Route::get('/', [UnifiedAttendanceController::class, 'index'])->name('index');
            Route::post('/log', [UnifiedAttendanceController::class, 'log'])->name('log');
            Route::get('/scan', [UnifiedAttendanceController::class, 'scan'])->name('scan');
            Route::get('/check', [UnifiedAttendanceController::class, 'check'])->name('check');
            Route::post('/save-reset', [UnifiedAttendanceController::class, 'saveAndReset'])->name('save-reset');
            Route::get('/realtime', [UnifiedAttendanceController::class, 'realtime'])->name('realtime');
            Route::get('/available-books', [UnifiedAttendanceController::class, 'availableBooks'])->name('available-books');
            Route::get('/books/colleges', [UnifiedAttendanceController::class, 'colleges'])->name('books.colleges');
            Route::post('/logout/initiate', [UnifiedAttendanceController::class, 'initiateLogout'])->name('logout.initiate');
            Route::post('/logout/confirm', [UnifiedAttendanceController::class, 'confirmLogout'])->name('logout.confirm');
            Route::post('/logout/verify', [UnifiedAttendanceController::class, 'verifyLogout'])->name('logout.verify');
            Route::post('/logout/resend', [UnifiedAttendanceController::class, 'resendLogoutCode'])->name('logout.resend');
        });

        // Settings: Logout 2FA toggle (outside attendance group)
        Route::get('settings/logout-2fa', [SettingsController::class, 'getTwoFactor'])->name('admin.settings.logout2fa.get');
        Route::post('settings/logout-2fa', [SettingsController::class, 'setTwoFactor'])->name('admin.settings.logout2fa.set');

        // Added route for attendance analytics
        Route::get('overdue/check-emails', [OverdueBookController::class, 'checkOverdueEmails'])->name('admin.overdue.check-emails');

        // Routes for overdue books
        Route::get('overdue-books', [OverdueBookController::class, 'getOverdueBooks'])->name('admin.overdue.books');
        Route::post('overdue/books/send-reminders', [OverdueBookController::class, 'sendReminders'])->name('admin.overdue.books.send-reminders');

        // Teachers/Visitors Attendance routes (OLD - Will be deprecated)
        Route::prefix('teachers-visitors-attendance')->name('admin.teachers_visitors_attendance.')->group(function () {
            Route::get('/', [TeachersVisitorsAttendanceController::class, 'index'])->name('index');
            Route::post('/log', [TeachersVisitorsAttendanceController::class, 'log'])->name('log');
            Route::get('/scan', [TeachersVisitorsAttendanceController::class, 'scan'])->name('scan');
            Route::get('/check', [TeachersVisitorsAttendanceController::class, 'check'])->name('check');
            Route::get('/realtime', [TeachersVisitorsAttendanceController::class, 'getRealtimeAttendance'])->name('realtime');
        });
    });
});
