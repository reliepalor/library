<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\AttendanceController;
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

        // Added route for attendance analytics
        Route::get('overdue/check-emails', [OverdueBookController::class, 'checkOverdueEmails'])->name('admin.overdue.check-emails');

        // Route for sending overdue book reminders
        Route::post('overdue/books/send-reminders', [OverdueBookController::class, 'sendReminders'])->name('admin.overdue.books.send-reminders');

        // Teachers/Visitors Attendance routes
        Route::prefix('teachers-visitors-attendance')->name('admin.teachers_visitors_attendance.')->group(function () {
            Route::get('/', [TeachersVisitorsAttendanceController::class, 'index'])->name('index');
            Route::post('/log', [TeachersVisitorsAttendanceController::class, 'log'])->name('log');
            Route::get('/scan', [TeachersVisitorsAttendanceController::class, 'scan'])->name('scan');
            Route::get('/check', [TeachersVisitorsAttendanceController::class, 'check'])->name('check');
            Route::get('/realtime', [TeachersVisitorsAttendanceController::class, 'getRealtimeAttendance'])->name('realtime');
        });
    });
});
