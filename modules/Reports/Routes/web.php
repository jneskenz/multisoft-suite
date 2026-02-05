<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')->where(['locale' => 'es|en'])->middleware(['web', 'setlocale', 'auth'])->group(function () {
    Route::prefix('reports')->name('reports.')->middleware(['can:access.reports'])->group(function () {
        Route::get('/', fn() => view('reports::index'))->name('index');
    });
});
