<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')->where(['locale' => 'es|en'])->middleware(['web', 'setlocale', 'auth'])->group(function () {
    Route::prefix('partners')->name('partners.')->middleware(['can:access.partners'])->group(function () {
        Route::get('/', fn() => view('partners::index'))->name('index');
    });
});
