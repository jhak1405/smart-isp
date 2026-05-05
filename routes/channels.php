<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Broadcast Channels para Smart ISP
|--------------------------------------------------------------------------
*/

// Canal privado para notificaciones al técnico
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para notificaciones a administradores
Broadcast::channel('admins', function ($user) {
    return $user->role === 'Administrador';
});
