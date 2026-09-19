# 🗺️ AksesKita — Laravel 11 Technical Architecture & Blueprint

Dokumen rancangan sistem ini disiapkan khusus sebagai panduan pengkodean (*development guide*) untuk pengembang atau **AI Coding Agent** (seperti Cursor, Windsurf, dll.).

## 1. Overview & Tech Stack Spesifikasi

* **Framework**: Laravel 12 (PHP 8.2+)

* **Frontend Stack**:

  * **Blade Templating** (Server-side rendered)

  * **Tailwind CSS v3** (Styling & layout)

  * **Alpine.js** (Ringan untuk interaksi UI & animasi mikro)

  * **Leaflet.js** (Peta interaktif 100% gratis)

* **Database**: Local MySQL via laragon

* **Autentikasi & Role**: Laravel Breeze / Spatie Laravel-Permission

  * **User (Masyarakat/Relawan)**: Mencari lokasi, filter rute, kirim laporan fasilitas.

  * **Admin (Verifikator)**: Moderasi laporan, verifikasi data tempat, kelola sistem.

## 2. Konsep UI/UX Homepage (Animated & Narrative-Driven)

Homepage dirancang dengan gaya **Storytelling & Human-Centric Editorial** (bukan dashboard kaku atau grid/bento berlebihan):

1. **Hero Section (Hero Dynamic Canvas)**:

   * Fullscreen visual headline dengan efek animasi teks bertahap (*Fade Up & Typing effect* via Alpine.js).

   * Widget Peta Mini Interaktif yang bisa di-drag/zoom langsung di hero section.

   * Interactive Mode Switcher: Tombol toggle visual untuk mensimulasikan pandangan **Mode Kursi Roda**, **Mode Tunanetra**, dan **Mode Lansia**.

2. **Interactive Live Demo Section**:

   * Tampilan simulasi indikator warna penanda (🟢 Hijau = Ramah, 🟡 Kuning = Terbatas, 🔴 Merah = Berbahaya).

   * Kartu pratinjau tempat (*Place Card*) dengan animasi *slide-in* saat kursor diarahkan.

3. **Interactive Problem vs Solution Story**:

   * Slider perbandingan sebelum & sesudah menggunakan AksesKita.

4. **Step-by-Step Micro Animations**:

   * 4 Langkah kontribusi warga dengan icon animasi mengambang (*floating icons*).

5. **Call-To-Action (CTA) CTA Banner**:

   * Tombol gabung kontributor dengan efek hover bercahaya (*glow accent*).

## 3. Database Schema Design (Local MySQL)

### `users`

| Column | Type | Description | 
 | ----- | ----- | ----- | 
| `id` | BigInt (PK) | Auto increment | 
| `name` | String | Nama lengkap | 
| `email` | String (Unique) | Email login | 
| `password` | String | Hashed password | 
| `role` | Enum | `'user'`, `'admin'` (default: `'user'`) | 
| `avatar` | String (Nullable) | Path foto profil | 
| `remember_token` | String | Session token | 
| `timestamps` | Timestamp | `created_at`, `updated_at` | 

### `places`

| Column | Type | Description | 
 | ----- | ----- | ----- | 
| `id` | BigInt (PK) | Auto increment | 
| `name` | String | Nama tempat/gedung/stasiun | 
| `slug` | String (Unique) | URL friendly name | 
| `category` | Enum | `'stasiun'`, `'mal'`, `'rumah_sakit'`, `'kafe'`, `'taman'`, `'fasilitas_publik'` | 
| `address` | Text | Alamat lengkap | 
| `latitude` | Decimal (10, 8) | Koordinat lokasi | 
| `longitude` | Decimal (11, 8) | Koordinat lokasi | 
| `overall_score` | Integer | Skor kelayakan 0 - 100 | 
| `status_color` | Enum | `'green'`, `'yellow'`, `'red'` | 
| `is_verified` | Boolean | Status verifikasi admin (default: `true` untuk seed data) | 
| `created_by` | BigInt (FK) | References `users.id` | 
| `timestamps` | Timestamp | `created_at`, `updated_at` | 

### `accessibility_features`

| Column | Type | Description | 
 | ----- | ----- | ----- | 
| `id` | BigInt (PK) | Auto increment | 
| `place_id` | BigInt (FK) | References `places.id` (Cascade Delete) | 
| `has_ramp` | Boolean | Ketersediaan bidang miring/ramp | 
| `has_lift` | Boolean | Lift ramah disabilitas | 
| `has_accessible_toilet` | Boolean | Toilet khusus disabilitas | 
| `has_guiding_block` | Boolean | Ubin pemandu tunanetra | 
| `wide_door` | Boolean | Pintu lebar (>90 cm) | 
| `braille_lift` | Boolean | Tombol lift berhuruf Braille | 
| `notes` | Text (Nullable) | Catatan tambahan | 

### `reports` (Laporan Fasilitas Rusak/Baru dari Warga)

| Column | Type | Description | 
 | ----- | ----- | ----- | 
| `id` | BigInt (PK) | Auto increment | 
| `user_id` | BigInt (FK) | References `users.id` | 
| `place_id` | BigInt (FK, Nullable) | References `places.id` jika memperbarui tempat | 
| `place_name` | String | Nama tempat yang dilaporkan | 
| `latitude` | Decimal (10, 8) | Koordinat laporan | 
| `longitude` | Decimal (11, 8) | Koordinat laporan | 
| `description` | Text | Deskripsi kondisi/hambatan | 
| `photo_path` | String | Path foto bukti fasilitas | 
| `status` | Enum | `'pending'`, `'approved'`, `'rejected'` | 
| `reviewed_by` | BigInt (FK, Nullable) | Admin yang memverifikasi | 
| `timestamps` | Timestamp | `created_at`, `updated_at` | 

## 4. Structure Directory Project (Laravel 11)

```
akseskita-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── Public/
│   │   │   │   ├── HomeController.php          # Homepage animasi & naratif
│   │   │   │   ├── MapController.php           # Peta utama & Filter AJAX/JSON
│   │   │   │   └── PlaceController.php         # Detail tempat & skor
│   │   │   ├── User/
│   │   │   │   ├── DashboardController.php     # Riwayat laporan user
│   │   │   │   └── ReportController.php        # Form upload laporan baru
│   │   │   └── Admin/
│   │   │       ├── AdminDashboardController.php # Statistik & moderasi
│   │   │       ├── ModerationController.php    # Approve/Reject laporan warga
│   │   │       └── PlaceManagementController.php # CRUD Data tempat
│   │   └── Middleware/
│   │       ├── IsAdmin.php                     # Proteksi role admin
│   │       └── IsUser.php                      # Proteksi role user
│   ├── Models/
│   │   ├── User.php
│   │   ├── Place.php
│   │   ├── AccessibilityFeature.php
│   │   └── Report.php
│   └── Services/
│       └── AccessibilityCalculator.php          # Logika hitung skor (0-100) & penentuan warna
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_places_table.php
│   │   ├── create_accessibility_features_table.php
│   │   └── create_reports_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminSeeder.php
│       └── PlaceSampleSeeder.php                # 20 Data dummy tempat di Jakarta/Tangerang
├── public/
│   ├── assets/
│   │   ├── images/                              # Asset ilustrasi & logo
│   │   └── js/
│   │       └── leaflet-map.js                   # Custom script peta Leaflet
├── resources/
│   ├── css/
│   │   └── app.css                              # Tailwind & Animasi CSS kustom
│   ├── js/
│   │   └── app.js                               # Alpine.js & Leaflet import
│   └── views/
│       ├── components/                          # Blade components reusable
│       │   ├── navbar.blade.php
│       │   ├── footer.blade.php
│       │   ├── place-card.blade.php
│       │   └── status-badge.blade.php
│       ├── layouts/
│       │   ├── app.blade.php                    # Layout publik
│       │   └── dashboard.blade.php              # Layout admin/user panel
│       ├── public/
│       │   ├── home.blade.php                   # Landing page animasi
│       │   ├── map.blade.php                    # Fullscreen Interactive Map
│       │   └── place-detail.blade.php           # Detail fasilitas & foto
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── user/
│       │   ├── dashboard.blade.php
│       │   └── create-report.blade.php
│       └── admin/
│           ├── dashboard.blade.php
│           ├── pending-reports.blade.php
│           └── places-index.blade.php
├── routes/
│   └── web.php                                  # Seluruh routing aplikasi
└── .env.example

```

## 5. Routing Architecture (`routes/web.php`)

```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\MapController;
use App\Http\Controllers\Public\PlaceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\ReportController as UserReport;
use App\Http\Controllers\Admin\AdminDashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\PlaceManagementController;

/*
|--------------------------------------------------------------------------
| Public Routes (Dapat diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/peta', [MapController::class, 'index'])->name('map.index');
Route::get('/api/places', [MapController::class, 'getPlacesJson'])->name('api.places'); // Data JSON untuk Leaflet
Route::get('/tempat/{slug}', [PlaceController::class, 'show'])->name('place.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| User Routes (Masyarakat / Kontributor)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'is_user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
    Route::get('/lapor', [UserReport::class, 'create'])->name('report.create');
    Route::post('/lapor', [UserReport::class, 'store'])->name('report.store');
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
    Route::post('/moderasi/{id}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
    Route::post('/moderasi/{id}/reject', [ModerationController::class, 'reject'])->name('moderation.reject');
    
    // CRUD Tempat
    Route::resource('places', PlaceManagementController::class);
});

```

## 6. Prompt Siap Pakai untuk AI Agent (Cursor / Windsurf)

Salin prompt di bawah ini untuk diserahkan ke AI Agent saat mulai mengoding:

> **AI Agent Prompt:**
> "Tolong buatkan project Laravel 12 bernama `akseskita-laravel` sesuai dengan file spesifikasi `akseskita_laravel_architecture.md`. Gunakan Laravel Blade, Tailwind CSS, Alpine.js, dan Leaflet.js. Buatlah migration database MySQL, Model, Seeder 20 tempat sampel di Jakarta/Tangerang, Authentication (User & Admin), Controller, dan Halaman Landing Page (`home.blade.php`) yang indah dengan animasi Alpine.js serta integrasi peta Leaflet.js."