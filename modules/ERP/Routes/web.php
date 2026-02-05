<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')->where(['locale' => 'es|en'])->middleware(['web', 'setlocale', 'auth'])->group(function () {
    Route::prefix('erp')->name('erp.')->middleware(['can:access.erp'])->group(function () {
        Route::get('/', fn() => view('erp::dashboard'))->name('dashboard');
        Route::get('/context', fn() => view('erp::context.select'))->name('context.select');
    });
});
