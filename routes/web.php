<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('app');
});

Route::get('/register', function () {
    return view('app');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('app');
    });
});