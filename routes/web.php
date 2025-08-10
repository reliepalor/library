<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\Auth\BooksController as AdminBooksController;
use App\Http\Controllers\Admin\Auth\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\User\AttendanceController as UserAttendanceController;
use App\Http\Controllers\User\UserBooksController;
use App\Http\Middleware\UserMiddleware;
use App\Http\Controllers\Admin\Auth\DashboardController;
use App\Http\Controllers\Admin\Auth\OverdueBookController;
use App\Http\Controllers\Admin\Auth\BorrowRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User profile routes
    Route::get('/user/profile', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');
});

/*---------------------------ROUTE FOR USER MIDDLEWARE------------------------------*/
Route::middleware(['auth', UserMiddleware::class])->group(function () {
    Route::get('/user', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    Route::get('/user/books', [UserBooksController::class, 'index'])->name('user.books.index');
    Route::get('/user/books/{id}', [UserBooksController::class, 'show'])->name('user.books.show');
    Route::post('/user/books/{id}/reserve', [UserBooksController::class, 'reserve'])->name('user.books.reserve');
    
    /*---------------------------ROUTE FOR USER ---ATTENDANCE------------------------------*/
    Route::prefix('user/attendance')->name('user.attendance.')->group(function () {
        Route::get('/', [UserAttendanceController::class, 'index'])->name('index');
        Route::post('/', [UserAttendanceController::class, 'store'])->name('store');
        Route::post('/log', [UserAttendanceController::class, 'log'])->name('log');
        Route::get('/scan', [UserAttendanceController::class, 'scan'])->name('scan');
        Route::get('/check', [UserAttendanceController::class, 'check'])->name('check');
    });

    Route::post('/user/borrow/request', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'store'])->name('user.borrow.request');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.auth.dashboard');

    // Overdue Book Routes - Moved here and fixed paths
    Route::post('admin/overdue-books/send-reminders', [OverdueBookController::class, 'sendReminders'])
        ->name('admin.overdue.books.send-reminders');
    Route::get('admin/overdue-books', [OverdueBookController::class, 'getOverdueBooks'])
        ->name('admin.overdue.books');

    /*---------------------------ROUTE FOR ADMIN ---ATTENDANCE------------------------------*/
    Route::prefix('admin/attendance')->name('admin.attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
        Route::post('/log', [AttendanceController::class, 'log'])->name('log');
        Route::get('/scan', [AttendanceController::class, 'scan'])->name('scan');
        Route::get('/check', [AttendanceController::class, 'check'])->name('check');
        // Correctly scoped name becomes 'admin.attendance.analytics'
        Route::get('/analytics', [AttendanceController::class, 'analytics'])->name('analytics');
        Route::get('/chart-data', [AttendanceController::class, 'getChartData'])->name('chart-data');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::post('/save-reset', [AttendanceController::class, 'saveAndReset'])->name('save-reset');
        Route::get('/history-data', [AttendanceController::class, 'getHistoryData'])->name('history-data');
        Route::get('/insights', [AttendanceController::class, 'insights'])->name('insights');
        Route::get('/insights-data', [AttendanceController::class, 'insightsData'])->name('insights-data');
        Route::get('/available-books', [AttendanceController::class, 'availableBooks'])->name('available-books');
    });

    /*---------------------------ROUTE FOR ADMIN ---BORROW REQUESTS------------------------------*/
    Route::prefix('admin/borrow')->name('admin.borrow.')->group(function () {
        Route::get('/requests', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'index'])->name('requests');
        Route::post('/requests/{id}/approve', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'reject'])->name('requests.reject');
        Route::post('/request', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'store'])->name('request');
    });

    /*---------------------------ROUTE FOR ADMIN ---STUDENTS------------------------------*/
    Route::get('students/{id}/qr', [AdminStudentController::class, 'generateStudentQr'])->name('students.qr');
    Route::get('admin/students/{id}/resend-qr', [AdminStudentController::class, 'resendQrCode'])->name('admin.students.resend-qr');
    Route::get('/admin/students/index', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/create', [AdminStudentController::class, 'create'])->name('admin.students.create');
    Route::post('admin/students', [AdminStudentController::class, 'store'])->name('admin.students.store');
    Route::get('admin/students/{id}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('admin/students/{id}', [AdminStudentController::class, 'update'])->name('admin.students.update');
    Route::post('/admin/students/{id}/archive', [AdminStudentController::class, 'archive'])->name('admin.students.archive');
    Route::post('/admin/students/{id}/unarchive', [AdminStudentController::class, 'unarchive'])->name('admin.students.unarchive');
    Route::get('/admin/students/archived', [AdminStudentController::class, 'archived'])->name('admin.students.archived');

    /*---------------------------ROUTE FOR ADMIN ---BOOKS------------------------------*/
    Route::prefix('admin/books')->name('admin.books.')->group(function () {
        Route::get('/', [AdminBooksController::class, 'index'])->name('index');
        Route::get('/create', [AdminBooksController::class, 'create'])->name('create');
        Route::post('/', [AdminBooksController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminBooksController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminBooksController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminBooksController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/archive', [AdminBooksController::class, 'archive'])->name('archive');
        Route::patch('/{id}/unarchive', [AdminBooksController::class, 'unarchive'])->name('unarchive');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/borrow-requests', [BorrowRequestController::class, 'index'])->name('borrow-requests.index');
        Route::post('/borrow-requests/{borrowRequest}/update-status', [BorrowRequestController::class, 'updateStatus'])->name('borrow-requests.update-status');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/admin-auth.php';

/*-------ROUTE FOR LOGIN/REGISTER----------*/
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('auth.login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

    Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');





