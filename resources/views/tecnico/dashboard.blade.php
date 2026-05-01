<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Técnicos - Smart ISP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <style>
        :root {
            --primary: #8b5cf6; /* Purple */
            --primary-hover: #7c3aed;
            --bg-color: #0f172a; /* Deep Slate */
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(139, 92, 246, 0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.1), transparent 25%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Header */
        header {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-weight: 600;
            font-size: 1.2rem;
            background: linear-gradient(to right, #c084fc, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logout-btn {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .logout-btn:hover {
            color: #ef4444;
        }

        /* Container */
        .container {
            padding: 1.5rem;
            max-width: 800px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* Glass Cards */
        .ticket-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }

        .ticket-card.en-proceso::before {
            background: var(--accent-warning);
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .ticket-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .ticket-client {
            font-size: 0.9rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.abierto { background: rgba(139, 92, 246, 0.2); color: #c084fc; border: 1px solid rgba(139, 92, 246, 0.3); }
        .badge.proceso { background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.3); }

        .ticket-desc {
            font-size: 0.95rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .ia-box {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px dashed rgba(139, 92, 246, 0.3);
            font-size: 0.85rem;
        }
        
        .ia-box strong { color: #c084fc; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.875rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            box-shadow: 0 4px 14px 0 rgba(139, 92, 246, 0.39);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.23);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-success), #059669);
            color: white;
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.39);
        }

        .btn-success:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.23);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        /* Modal / Resolution Form */
        .resolution-form {
            display: none;
            animation: slideDown 0.3s ease-out forwards;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--glass-border);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
        }

        .form-control {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 0.75rem;
            color: white;
            font-family: 'Outfit', sans-serif;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        /* Custom File Input */
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 1rem;
            background: rgba(139, 92, 246, 0.1);
            border: 1px dashed var(--primary);
            border-radius: 8px;
            color: var(--primary);
            font-weight: 500;
            text-align: center;
            transition: all 0.3s;
        }

        .file-upload:hover .file-upload-label {
            background: rgba(139, 92, 246, 0.2);
        }

        /* Map Container */
        .map-container {
            height: 200px;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            margin-bottom: 0.5rem;
        }

        .gps-status {
            font-size: 0.8rem;
            color: var(--accent-warning);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .gps-status.locked {
            color: var(--accent-success);
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <header>
        <div class="header-title">Smart ISP - Técnicos</div>
        <form method="POST" action="/admin/logout" style="display:inline;">
            @csrf
            <button type="submit" class="logout-btn" style="background:none;border:none;cursor:pointer;font-family:inherit;">Cerrar sesión</button>
        </form>
    </header>

    <div class="container">
        <h1 class="page-title">Tus Tickets Asignados</h1>

        @if(session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($tickets->isEmpty())
            <div style="text-align:center; padding: 3rem 1rem; color: var(--text-muted);">
                <svg style="width:64px;height:64px;margin:0 auto 1rem;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p>¡Buen trabajo! No tienes tickets pendientes.</p>
            </div>
        @else
            @foreach($tickets as $ticket)
                <div class="ticket-card {{ $ticket->estado == 'En Proceso' ? 'en-proceso' : '' }}">
                    <div class="ticket-header">
                        <div>
                            <h2 class="ticket-title">#{{ $ticket->id }} - {{ $ticket->titulo }}</h2>
                            <div class="ticket-client">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $ticket->cliente ? $ticket->cliente->nombre_completo : 'Cliente Desconocido' }}
                                @if($ticket->cliente && $ticket->cliente->direccion_escrita)
                                    | {{ $ticket->cliente->direccion_escrita }}
                                @endif
                            </div>
                        </div>
                        <span class="badge {{ $ticket->estado == 'Abierto' ? 'abierto' : 'proceso' }}">{{ $ticket->estado }}</span>
                    </div>

                    <div class="ticket-desc">
                        {{ $ticket->descripcion }}
                    </div>

                    @if($ticket->ia_resumen || $ticket->ia_categoria)
                        <div class="ia-box">
                            <strong>IA:</strong> {{ $ticket->ia_categoria }} - {{ $ticket->ia_prioridad }}<br>
                            <em>{{ $ticket->ia_resumen }}</em>
                        </div>
                    @endif

                    @if($ticket->estado === 'Abierto')
                        <form action="{{ route('tecnico.ticket.status', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline">Iniciar Trabajo</button>
                        </form>
                    @elseif($ticket->estado === 'En Proceso')
                        <button type="button" class="btn btn-primary" onclick="toggleResolveForm({{ $ticket->id }})">
                            Resolver Ticket
                        </button>

                        <!-- Formulario de Resolución -->
                        <div id="resolve-form-{{ $ticket->id }}" class="resolution-form">
                            <form action="{{ route('tecnico.ticket.resolver', $ticket->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm({{ $ticket->id }})">
                                @csrf
                                
                                <div class="form-group">
                                    <label class="form-label">Evidencia Fotográfica *</label>
                                    <div class="file-upload">
                                        <div class="file-upload-label" id="file-label-{{ $ticket->id }}">
                                            <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Tomar Foto o Subir Archivo
                                        </div>
                                        <input type="file" name="evidencia" accept="image/*" capture="environment" required onchange="updateFileName(this, {{ $ticket->id }})">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Nota del Técnico (Opcional)</label>
                                    <textarea name="nota_tecnico" class="form-control" rows="2" placeholder="Describe brevemente la solución..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Tu Ubicación (GPS) *</label>
                                    <div id="map-{{ $ticket->id }}" class="map-container"></div>
                                    <div id="gps-status-{{ $ticket->id }}" class="gps-status">
                                        <svg style="width:16px;height:16px;animation: spin 2s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Buscando señal GPS...
                                    </div>
                                    <input type="hidden" name="latitud" id="lat-{{ $ticket->id }}" required>
                                    <input type="hidden" name="longitud" id="lng-{{ $ticket->id }}" required>
                                </div>

                                <button type="submit" class="btn btn-success" id="submit-btn-{{ $ticket->id }}" disabled>
                                    Completar y Resolver
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        // Animations
        const style = document.createElement('style');
        style.innerHTML = `@keyframes spin { 100% { transform: rotate(360deg); } }`;
        document.head.appendChild(style);

        function toggleResolveForm(ticketId) {
            const form = document.getElementById('resolve-form-' + ticketId);
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                form.style.display = 'block';
                initMap(ticketId);
            }
        }

        function updateFileName(input, ticketId) {
            const label = document.getElementById('file-label-' + ticketId);
            if (input.files && input.files.length > 0) {
                label.innerHTML = `
                    <svg style="width:24px;height:24px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Imagen lista (${(input.files[0].size / 1024 / 1024).toFixed(2)} MB)
                `;
                label.style.borderColor = '#10b981';
                label.style.background = 'rgba(16, 185, 129, 0.1)';
            }
        }

        let maps = {};

        function initMap(ticketId) {
            if (maps[ticketId]) return; // Evitar reinicializar

            // Crear mapa
            const map = L.map('map-' + ticketId).setView([-5.19449, -80.63282], 13); // Default Piura
            maps[ticketId] = map;

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            let marker = null;

            // Obtener ubicación GPS real
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    map.setView([lat, lng], 16);
                    
                    if (marker) map.removeLayer(marker);
                    
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup("Tu ubicación actual (Precisión: " + Math.round(accuracy) + "m)").openPopup();
                    
                    L.circle([lat, lng], { radius: accuracy, color: '#8b5cf6', fillOpacity: 0.1 }).addTo(map);

                    // Guardar en inputs ocultos
                    document.getElementById('lat-' + ticketId).value = lat;
                    document.getElementById('lng-' + ticketId).value = lng;

                    // Actualizar UI
                    const statusEl = document.getElementById('gps-status-' + ticketId);
                    statusEl.className = 'gps-status locked';
                    statusEl.innerHTML = `
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Ubicación fijada con éxito
                    `;

                    // Habilitar botón de submit
                    document.getElementById('submit-btn-' + ticketId).disabled = false;

                }, function(error) {
                    const statusEl = document.getElementById('gps-status-' + ticketId);
                    statusEl.innerHTML = `
                        <svg style="width:16px;height:16px;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span style="color:#ef4444;">Error al obtener GPS. Asegúrate de dar permisos de ubicación.</span>
                    `;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                document.getElementById('gps-status-' + ticketId).innerHTML = "Tu navegador no soporta geolocalización.";
            }
        }

        function validateForm(ticketId) {
            const lat = document.getElementById('lat-' + ticketId).value;
            const lng = document.getElementById('lng-' + ticketId).value;
            
            if (!lat || !lng) {
                alert('Es necesario capturar la ubicación GPS para resolver el ticket.');
                return false;
            }
            
            document.getElementById('submit-btn-' + ticketId).innerHTML = 'Guardando...';
            document.getElementById('submit-btn-' + ticketId).disabled = true;
            return true;
        }
    </script>
</body>
</html>
