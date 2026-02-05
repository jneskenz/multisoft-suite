<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')->where(['locale' => 'es|en'])->middleware(['web', 'setlocale', 'auth'])->group(function () {
    Route::prefix('fms')->name('fms.')->middleware(['can:access.fms'])->group(function () {
        Route::get('/', fn() => view('fms::dashboard'))->name('dashboard');
    });
});
