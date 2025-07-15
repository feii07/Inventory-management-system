<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/users', function () {
    return view('users.index');
})->name('users.index');

Route::get('/roles', function () {
    return view('roles.index');
})->name('roles.index');

Route::get('/items', function () {
    return view('items.index');
})->name('items.index');

Route::get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');