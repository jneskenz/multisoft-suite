<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')->where(['locale' => 'es|en'])->middleware(['web', 'setlocale', 'auth'])->group(function () {
    Route::prefix('hr')->name('hr.')->middleware(['can:access.hr'])->group(function () {
        Route::get('/', fn() => view('hr::dashboard'))->name('dashboard');
    });
});
