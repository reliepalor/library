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
use App\Http\Controllers\Admin\Auth\CampusNewsController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;

Route::get('/', [HomeController::class, 'index']);

// Public campus news routes
Route::get('/campus-news/{campusNews}', [HomeController::class, 'showNews'])->name('campus-news.show');

Route::get('/services', [App\Http\Controllers\ServicesController::class, 'index'])->name('services.index');
Route::post('/services/register-qr', [App\Http\Controllers\ServicesController::class, 'registerQr'])->name('services.register-qr');

Route::get('/dashboard', [UserDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('/user', [UserDashboardController::class, 'index'])->name('user.dashboard');

    Route::get('/user/books', [UserBooksController::class, 'index'])->name('user.books.index');
    Route::get('/user/books/{id}', [UserBooksController::class, 'show'])->name('user.books.show');
    Route::post('/user/books/{id}/reserve', [UserBooksController::class, 'reserve'])->name('user.books.reserve');

    /*---------------------------ROUTE FOR USER ---ATTENDANCE------------------------------*/
    Route::prefix('user/attendance')->name('user.attendance.')->group(function () {
        Route::get('/', [UserAttendanceController::class, 'index'])->name('index');
        Route::post('/', [UserAttendanceController::class, 'store'])->name('store');
        Route::post('/log', [UserAttendanceController::class, 'log'])->name('log');
        // Realtime data for user attendance page
        Route::get('/realtime', [UserAttendanceController::class, 'realtime'])->name('realtime');
        Route::get('/scan', [UserAttendanceController::class, 'scan'])->name('scan');
        Route::get('/check', [UserAttendanceController::class, 'check'])->name('check');
    });

    Route::post('/user/borrow/request', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'store'])->name('user.borrow.request');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.auth.dashboard');

    // Real-time attendance data
    Route::get('admin/attendance/realtime', [\App\Http\Controllers\Admin\AttendanceController::class, 'getRealtimeAttendance'])
        ->name('admin.attendance.realtime');

    // Overdue Book Routes are defined in routes/admin-auth.php to avoid duplication

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
        Route::get('/books/colleges', [AttendanceController::class, 'booksColleges'])->name('books.colleges');
    });

    /*---------------------------ROUTE FOR ADMIN ---BORROW REQUESTS------------------------------*/
    Route::prefix('admin/borrow')->name('admin.borrow.')->group(function () {
        Route::get('/requests', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'index'])->name('requests');
        Route::post('/requests/{id}/approve', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'reject'])->name('requests.reject');
        Route::post('/requests/{id}/return', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'markAsReturned'])->name('requests.return');
        Route::post('/request', [App\Http\Controllers\Admin\Auth\BorrowRequestController::class, 'store'])->name('request');
    });

    /*---------------------------ROUTE FOR ADMIN ---STUDENTS------------------------------*/
    Route::get('students/{id}/qr', [AdminStudentController::class, 'generateStudentQr'])->name('students.qr');
    Route::post('admin/students/{id}/resend-qr', [AdminStudentController::class, 'resendQrCode'])->name('admin.students.resend-qr');
    Route::get('/admin/students/index', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/create', [AdminStudentController::class, 'create'])->name('admin.students.create');
    Route::post('admin/students', [AdminStudentController::class, 'store'])->name('admin.students.store');
    Route::get('admin/students/{id}/edit', [AdminStudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('admin/students/{id}', [AdminStudentController::class, 'update'])->name('admin.students.update');
    Route::post('/admin/students/{id}/archive', [AdminStudentController::class, 'archive'])->name('admin.students.archive');
    Route::patch('/admin/students/{id}/unarchive', [AdminStudentController::class, 'unarchive'])->name('admin.students.unarchive');
    Route::get('/admin/students/archived', [AdminStudentController::class, 'archived'])->name('admin.students.archived');

    /*---------------------------ROUTE FOR ADMIN ---TEACHERS/VISITORS------------------------------*/
    Route::get('teachers_visitors/{id}/qr', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'generateTeacherVisitorQr'])->name('teachers_visitors.qr');
    Route::post('admin/teachers_visitors/{id}/resend-qr', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'resendQrCode'])->name('admin.teachers_visitors.resend-qr');
    Route::get('/admin/teachers_visitors/index', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'index'])->name('admin.teachers_visitors.index');
    Route::get('/admin/teachers_visitors/create', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'create'])->name('admin.teachers_visitors.create');
    Route::post('admin/teachers_visitors', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'store'])->name('admin.teachers_visitors.store');
    Route::get('admin/teachers_visitors/{id}/edit', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'edit'])->name('admin.teachers_visitors.edit');
    Route::put('admin/teachers_visitors/{id}', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'update'])->name('admin.teachers_visitors.update');
    Route::post('/admin/teachers_visitors/{id}/archive', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'archive'])->name('admin.teachers_visitors.archive');
    Route::post('/admin/teachers_visitors/{id}/unarchive', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'unarchive'])->name('admin.teachers_visitors.unarchive');
    Route::get('/admin/teachers_visitors/archived', [App\Http\Controllers\Admin\Auth\TeacherVisitorController::class, 'archived'])->name('admin.teachers_visitors.archived');

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

    /*---------------------------ROUTE FOR ADMIN ---CAMPUS NEWS------------------------------*/
    Route::prefix('admin/campus-news')->name('admin.campus-news.')->group(function () {
        Route::get('/', [CampusNewsController::class, 'index'])->name('index');
        Route::get('/create', [CampusNewsController::class, 'create'])->name('create');
        Route::post('/', [CampusNewsController::class, 'store'])->name('store');
        Route::get('/{campusNews}', [CampusNewsController::class, 'show'])->name('show');
        Route::get('/{campusNews}/edit', [CampusNewsController::class, 'edit'])->name('edit');
        Route::put('/{campusNews}', [CampusNewsController::class, 'update'])->name('update');
        Route::delete('/{campusNews}', [CampusNewsController::class, 'destroy'])->name('destroy');
        Route::patch('/{campusNews}/status', [CampusNewsController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{campusNews}/featured', [CampusNewsController::class, 'toggleFeatured'])->name('toggle-featured');
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
