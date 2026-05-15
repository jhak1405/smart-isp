<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TecnicoLoginController;
use App\Http\Controllers\TecnicoController;

Route::get('/', function () {
    return view('welcome');
});

// Login universal: Redirigimos siempre al login de Filament
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Logout universal para técnicos
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware(['auth', 'role:Tecnico'])->group(function () {
    Route::get('/tecnico', [TecnicoController::class, 'dashboard'])->name('tecnico.dashboard');
    Route::post('/tecnico/ticket/{id}/status', [TecnicoController::class, 'updateStatus'])->name('tecnico.ticket.status');
    Route::post('/tecnico/ticket/{id}/resolver', [TecnicoController::class, 'resolver'])->name('tecnico.ticket.resolver');
    Route::post('/tecnico/notifications/read', [TecnicoController::class, 'markNotificationsAsRead'])->name('tecnico.notifications.read');
    Route::get('/tecnico/notifications', [TecnicoController::class, 'getNotifications'])->name('tecnico.notifications.get');
    Route::post('/tecnico/pendiente/{id}/completar', [TecnicoController::class, 'completarPendiente'])->name('tecnico.pendiente.completar');
});
