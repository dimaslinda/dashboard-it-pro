<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Asset Survey routes will be handled by Filament Admin Panel
