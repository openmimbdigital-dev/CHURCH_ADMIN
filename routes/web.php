<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Roles\Index as AdminRolesIndex;
use App\Livewire\Admin\Users\Form as AdminUsersForm;
use App\Livewire\Admin\Users\Index as AdminUsersIndex;
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
});
