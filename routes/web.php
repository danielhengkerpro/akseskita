<?php

use App\Http\Controllers\Admin\AdminDashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\PlaceManagementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\MapController;
use App\Http\Controllers\Public\PlaceController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\PlaceController as UserPlace;
use App\Http\Controllers\User\ReportController as UserReport;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Dapat diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/peta', [MapController::class, 'index'])->name('map.index');
Route::get('/api/places', [MapController::class, 'getPlacesJson'])
    ->middleware('throttle:60,1')
    ->name('api.places'); // Data JSON untuk Leaflet
Route::get('/tempat/{place:slug}', [PlaceController::class, 'show'])->name('place.show');

/*
|--------------------------------------------------------------------------
| User Authentication Routes (Masyarakat / Kontributor)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes (Verifikator & Administrator Terpisah)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:5,1');
    });
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| User Routes (Masyarakat / Disabilitas / Kontributor)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'is_user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
    Route::post('/preferensi', [UserDashboard::class, 'updatePreferences'])->name('preferences.update');
    Route::get('/lapor', [UserReport::class, 'create'])->name('report.create');
    Route::post('/lapor', [UserReport::class, 'store'])->name('report.store');
    Route::get('/tempat/tambah', [UserPlace::class, 'create'])->name('places.create');
    Route::post('/tempat', [UserPlace::class, 'store'])->name('places.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Verifikator & Pengelola Platform)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Moderasi Laporan Warga
    Route::get('/moderasi', [ModerationController::class, 'index'])->name('moderation.index');
    Route::post('/moderasi/{report}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
    Route::post('/moderasi/{report}/reject', [ModerationController::class, 'reject'])->name('moderation.reject');

    // Verifikasi & Pengelolaan Tempat
    Route::post('/places/{place}/verify', [PlaceManagementController::class, 'verify'])->name('places.verify');
    Route::post('/places/{place}/reject', [PlaceManagementController::class, 'reject'])->name('places.reject');
    Route::resource('places', PlaceManagementController::class)->except('show');
});
