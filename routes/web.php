<?php

use App\Http\Controllers\MatrixController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MatrixController::class, 'index'])->name('matrix.index');
Route::post('/matrix/store', [MatrixController::class, 'store'])->name('matrix.store');
Route::get('/matrix/export', [MatrixController::class, 'export'])->name('matrix.export');