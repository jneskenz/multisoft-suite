<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')->where(['locale' => 'es|en'])->middleware(['web', 'setlocale', 'auth'])->group(function () {
    Route::prefix('crm')->name('crm.')->middleware(['can:access.crm'])->group(function () {
        Route::get('/', fn() => view('crm::dashboard'))->name('dashboard');
    });
});
