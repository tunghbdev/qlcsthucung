<?php

use Illuminate\Support\Facades\Route;

// Vue SPA routes - exclude API routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api/).*');

