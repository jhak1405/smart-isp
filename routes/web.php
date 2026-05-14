<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TecnicoLoginController;
use App\Http\Controllers\TecnicoController;

Route::get('/', function () {
    return view('welcome');
});

// Login universal (técnicos y administradores)
Route::get('/login',  [TecnicoLoginController::class, 'showForm'])->name('login');
Route::post('/login', [TecnicoLoginController::class, 'login'])->name('login.post');
Route::post('/logout',[TecnicoLoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:Tecnico'])->group(function () {
    Route::get('/tecnico', [TecnicoController::class, 'dashboard'])->name('tecnico.dashboard');
    Route::post('/tecnico/ticket/{id}/status', [TecnicoController::class, 'updateStatus'])->name('tecnico.ticket.status');
    Route::post('/tecnico/ticket/{id}/resolver', [TecnicoController::class, 'resolver'])->name('tecnico.ticket.resolver');
    Route::post('/tecnico/notifications/read', [TecnicoController::class, 'markNotificationsAsRead'])->name('tecnico.notifications.read');
    Route::get('/tecnico/notifications', [TecnicoController::class, 'getNotifications'])->name('tecnico.notifications.get');
    Route::post('/tecnico/pendiente/{id}/completar', [TecnicoController::class, 'completarPendiente'])->name('tecnico.pendiente.completar');
});
