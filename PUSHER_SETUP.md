# Configuración de Broadcasting y Pusher para Smart ISP

## Variables de entorno a añadir en .env

Para que el sistema de notificaciones en tiempo real funcione, necesitas configurar las siguientes variables en tu archivo `.env`:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=pusher
BROADCAST_CONNECTION=default

# Pusher Configuration (obtén estas credenciales desde https://dashboard.pusher.com)
PUSHER_APP_ID=tu_app_id
PUSHER_APP_KEY=tu_app_key
PUSHER_APP_SECRET=tu_app_secret
PUSHER_APP_CLUSTER=mt1

# Queue Configuration (para disparar los Jobs en segundo plano)
QUEUE_CONNECTION=database
```

## Pasos para obtener credenciales de Pusher

1. Ve a https://dashboard.pusher.com
2. Crea una nueva app (o usa una existente)
3. En la sección "App Keys", copia:
   - `App ID` → `PUSHER_APP_ID`
   - `Key` → `PUSHER_APP_KEY`
   - `Secret` → `PUSHER_APP_SECRET`
   - `Cluster` → `PUSHER_APP_CLUSTER`

## Configuración del servidor de colas

Para que los Jobs se procesen en segundo plano, ejecuta en otra terminal:

```bash
php artisan queue:work
```

## Canales privados configurados

El sistema utiliza dos canales privados de Pusher:

1. **`private-user.{user_id}`** - Para notificaciones al técnico asignado
2. **`private-admins`** - Para notificaciones a los administradores

Estos canales requieren autenticación y están protegidos mediante el middleware de Laravel.

## Testing del sistema

1. **Como Admin** (en Filament):
   - Crea o asigna un ticket a un técnico
   - El técnico debería ver la notificación en la campanita sin recargar la página

2. **Como Técnico** (en dashboard personalizado):
   - Abre tu dashboard
   - Verás la campanita en la esquina superior derecha
   - Al resolver un ticket, los admins recibirán una notificación en Filament

## Troubleshooting

Si las notificaciones no llegan:

1. Verifica que `QUEUE_CONNECTION` esté configurado como `database`
2. Asegúrate de que el worker está corriendo: `php artisan queue:work`
3. Verifica las credenciales de Pusher en tu dashboard
4. Revisa los logs: `tail -f storage/logs/laravel.log`
5. En el navegador, abre DevTools (F12) → Console y busca errores de Pusher

## Estructura del flujo

```
Admin (Filament)
    ↓
Asigna Ticket
    ↓
TicketObserver detecta cambio
    ↓
Dispara Job: BroadcastTicketAssigned
    ↓
Job lanza Event: TicketAssignedToTechnician
    ↓
Event broadcast a Pusher
    ↓
Técnico recibe evento vía Laravel Echo
    ↓
Campanita se actualiza automáticamente
```
