<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Businesses\Index as AdminBusinessesIndex;
use App\Livewire\Admin\Churches\Form as AdminChurchesForm;
use App\Livewire\Admin\Churches\Index as AdminChurchesIndex;
use App\Livewire\Admin\Churches\Show as AdminChurchesShow;
use App\Livewire\Admin\Roles\Index as AdminRolesIndex;
use App\Livewire\Admin\Users\Form as AdminUsersForm;
use App\Livewire\Admin\Users\Index as AdminUsersIndex;
use App\Livewire\Admin\Zones\Form as AdminZonesForm;
use App\Livewire\Admin\Zones\Index as AdminZonesIndex;
use App\Livewire\Admin\Zones\Show as AdminZonesShow;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::middleware('permission:users.view')->group(function () {
        Route::get('/admin/users', AdminUsersIndex::class)->name('admin.users.index');
    });

    Route::middleware('permission:users.create')->group(function () {
        Route::get('/admin/users/create', AdminUsersForm::class)->name('admin.users.create');
    });

    Route::middleware('permission:users.edit')->group(function () {
        Route::get('/admin/users/{user}/edit', AdminUsersForm::class)->name('admin.users.edit');
    });

    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/admin/roles', AdminRolesIndex::class)->name('admin.roles.index');
    });

    Route::middleware('permission:businesses.view')->group(function () {
        Route::get('/admin/businesses', AdminBusinessesIndex::class)->name('admin.businesses.index');
    });

    Route::middleware('permission:zones.create')->group(function () {
        Route::get('/admin/zones/create', AdminZonesForm::class)->name('admin.zones.create');
    });

    Route::middleware('permission:zones.edit')->group(function () {
        Route::get('/admin/zones/{zone}/edit', AdminZonesForm::class)->name('admin.zones.edit');
    });

    Route::middleware('permission:zones.view')->group(function () {
        Route::get('/admin/zones', AdminZonesIndex::class)->name('admin.zones.index');
        Route::get('/admin/zones/{zone}', AdminZonesShow::class)->name('admin.zones.show');
    });

    Route::middleware('permission:churches.create')->group(function () {
        Route::get('/admin/churches/create', AdminChurchesForm::class)->name('admin.churches.create');
    });

    Route::middleware('permission:churches.edit')->group(function () {
        Route::get('/admin/churches/{church}/edit', AdminChurchesForm::class)->name('admin.churches.edit');
    });

    Route::middleware('permission:churches.view')->group(function () {
        Route::get('/admin/churches', AdminChurchesIndex::class)->name('admin.churches.index');
        Route::get('/admin/churches/{church}', AdminChurchesShow::class)->name('admin.churches.show');
    });
});
