<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Main & login routes
Route::get('/', function () { return redirect('/login'); })->name('home');
Route::get('/login', [LoginController::class, 'gLogin'])->name('login');
Route::post('/login', [LoginController::class, 'pLogin'])->name('login-form');

// Min. role 0
Route::middleware(['auth', 'role:0'])->group(function () {
    // Logout routes
    Route::get('/logout', [LoginController::class, 'pLogout'])->name('logout');
    Route::post('/logout', [LoginController::class, 'pLogout'])->name('logout');

    // Register routes
    Route::get('/register', [RegisterController::class, 'gRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'pRegister'])->name('register-form');
});

// Min. role 1 routes
Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'gDashboard'])->name('dashboard');

    Route::get('/drivers', [DriverController::class, 'gDrivers'])->name('drivers');
    Route::get('/drivers/{id}', [DriverController::class, 'gDriver'])->name('driver');

    Route::get('/profile', [ProfileController::class, 'gProfile'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'pProfile'])->name('profile-form');
    
    // API
    Route::get('/api/md/{id}', [ApiController::class, 'gMatchingDays']);
    Route::get('/api/cal/{cal}', [ApiController::class, 'gCalendar']);
    Route::get('/api/drivers', [ApiController::class, 'gDrivers']);
});

// Min. role 2 routes
Route::middleware(['auth', 'role:2'])->group(function () {
    // API
    Route::get('/api/clear-md', [ApiController::class, 'gClearMatchingDays']);
});