<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TecnicoController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/tecnico', [TecnicoController::class, 'dashboard'])->name('tecnico.dashboard');
    Route::post('/tecnico/ticket/{id}/status', [TecnicoController::class, 'updateStatus'])->name('tecnico.ticket.status');
    Route::post('/tecnico/ticket/{id}/resolver', [TecnicoController::class, 'resolver'])->name('tecnico.ticket.resolver');
});
