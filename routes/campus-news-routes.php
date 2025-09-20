<?php

use App\Http\Controllers\Admin\Auth\CampusNewsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin/campus-news')->name('admin.campus-news.')->group(function () {
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
