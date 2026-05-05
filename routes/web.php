<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Collecte\CreateCollecte;
use App\Livewire\Collecte\ListCollectes;
use App\Livewire\Collecte\ShowCollecte;
use App\Livewire\Profile\EditProfile;
use App\Livewire\Analyse\AnalyseCollecte;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard avec Livewire
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/collectes', ListCollectes::class)->name('collectes.list');
    Route::get('/collecte/creer', CreateCollecte::class)->name('collecte.create');
    Route::get('/profile', EditProfile::class)->name('profile.edit');
    Route::get('/collecte/{id}', ShowCollecte::class)->name('collecte.show');
    Route::get('/analyse/{id}', AnalyseCollecte::class)->name('analyse.show');

});

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\AdminUsers;
use App\Livewire\Admin\AdminCollectes;
use App\Livewire\Admin\AdminSettings;

// Routes Admin (protégées)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/utilisateurs', AdminUsers::class)->name('users');
    Route::get('/collectes', AdminCollectes::class)->name('collectes');
    Route::get('/parametres', AdminSettings::class)->name('settings');
});
