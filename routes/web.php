<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CityPageController;
use App\Http\Controllers\DistrictPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Pelatihan P3K
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public Articles
Route::get('/artikel', [PublicArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{slug}', [PublicArticleController::class, 'show'])->name('articles.show');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Panel Routes (Protected by Auth & Admin verification)
Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminArticleController::class, 'index'])->name('dashboard');
        Route::resource('articles', AdminArticleController::class)->except(['show']);
        Route::post('articles/ai-generate', [AdminArticleController::class, 'aiGenerate'])->name('articles.ai-generate');
        Route::post('upload-image', [AdminArticleController::class, 'uploadImage'])->name('upload.image');
    });

// Dynamic City Landing Pages
Route::get('/kota-{citySlug}', [CityPageController::class, 'show'])->name('city.landing');
Route::get('/pelatihan/kota-{citySlug}', [CityPageController::class, 'show']);
Route::get('/kota/{citySlug}', [CityPageController::class, 'show']);

// Dynamic District Landing Pages
Route::get('/kecamatan-{districtSlug}', [DistrictPageController::class, 'show'])->name('kecamatan.landing');
Route::get('/pelatihan/kecamatan-{districtSlug}', [DistrictPageController::class, 'show']);
Route::get('/kecamatan/{districtSlug}', [DistrictPageController::class, 'show']);
Route::get('/kota-{citySlug}/kecamatan-{districtSlug}', [DistrictPageController::class, 'showNested'])->name('district.show');
