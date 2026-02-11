<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;

Route::get('/', [QuoteController::class, 'index'])->name('quotes.index');
Route::get('/quote/{id}', [QuoteController::class, 'show'])->name('quotes.show');
Route::get('/quote/{id}/image', [QuoteController::class, 'downloadImage'])->name('quotes.image');
