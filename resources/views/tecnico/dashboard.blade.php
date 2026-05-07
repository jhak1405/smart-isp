<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal de Técnicos - Smart ISP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;

            /* Text */
            --text-main: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;

            /* Status Accents */
            --success: #10b981;
            --success-bg: #ecfdf5;
            --warning: #f59e0b;
            --warning-bg: #fffbeb;
            --danger: #ef4444;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Header */
        header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .header-title {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-title svg {
            color: var(--primary);
            width: 24px;
            height: 24px;
        }

        .logout-btn {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .logout-btn:hover {
            color: var(--danger);
        }

        /* Container */
        .container {
            padding: 2rem 1.25rem;
            max-width: 800px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .ticket-count {
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        /* Cards */
        .ticket-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
            position: relative;
        }

        .ticket-card:hover {
            box-shadow: var(--shadow-md);
            border-color: #cbd5e1;
        }

        .ticket-card.en-proceso {
            border-left: 4px solid var(--warning);
        }

        .ticket-card.abierto {
            border-left: 4px solid var(--primary);
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .ticket-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.35rem;
            line-height: 1.4;
        }

        .ticket-client {
            font-size: 0.9rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ticket-client svg {
            color: var(--text-muted);
        }

        /* Badges */
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .badge.abierto { background: var(--primary-light); color: var(--primary); }
        .badge.proceso { background: var(--warning-bg); color: #d97706; }

        .ticket-desc {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-bottom: 1.25rem;
            line-height: 1.6;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }

        .ia-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.25rem;
            border: 1px solid var(--border-color);
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .ia-icon {
            background: var(--primary-light);
            color: var(--primary);
            padding: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ia-content {
            flex: 1;
        }

        .ia-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }

        .ia-summary {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: 1px solid var(--primary-hover);
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
            border: 1px solid #059669;
            box-shadow: var(--shadow-sm);
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-outline {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            box-shadow: var(--shadow-sm);
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
        }

        /* Forms */
        .resolution-form {
            display: none;
            animation: fadeIn 0.3s ease-out forwards;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            resize: vertical;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Custom File Input */
        .file-upload {
            position: relative;
            display: block;
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
            z-index: 10;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 1.5rem;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .file-upload-label svg {
            color: var(--text-muted);
            width: 32px;
            height: 32px;
        }

        .file-upload:hover .file-upload-label {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary);
        }

        .file-upload:hover .file-upload-label svg {
            color: var(--primary);
        }

        /* Map */
        .map-container {
            height: 220px;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            margin-bottom: 0.5rem;
            z-index: 1;
        }

        .gps-status {
            font-size: 0.85rem;
            color: var(--warning);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            padding: 8px 12px;
            background: var(--warning-bg);
            border-radius: 6px;
        }

        .gps-status.locked {
            color: #059669;
            background: var(--success-bg);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
            background: var(--bg-card);
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            color: #cbd5e1;
        }

        .empty-state h3 {
            font-size: 1.125rem;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            background: var(--success-bg);
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </style>
</head>
<body>

    <header>
        <div class="header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Smart ISP Tech
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            @include('components.notification-bell')
            <form method="POST" action="/admin/logout" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn" style="background:none;border:none;cursor:pointer;font-family:inherit;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Salir
                </button>
            </form>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Tickets Asignados</h1>
            <span class="ticket-count">{{ $tickets->count() }} {{ $tickets->count() == 1 ? 'Pendiente' : 'Pendientes' }}</span>
        </div>

        @if($errors->any())
            <div class="alert" style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($tickets->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <h3>Estás al día</h3>
                <p>No tienes tickets pendientes en tu bandeja por ahora.</p>
            </div>
        @else
            @foreach($tickets as $ticket)
                <div class="ticket-card {{ $ticket->estado == 'En Proceso' ? 'en-proceso' : 'abierto' }}">
                    <div class="ticket-header">
                        <div>
                            <h2 class="ticket-title">#{{ $ticket->id }} - {{ $ticket->titulo }}</h2>
                            <div class="ticket-client">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $ticket->cliente ? $ticket->cliente->nombre_completo : 'Cliente Desconocido' }}
                                @if($ticket->cliente && $ticket->cliente->direccion_escrita)
                                    • {{ $ticket->cliente->direccion_escrita }}
                                @endif
                            </div>
                        </div>
                        <span class="badge {{ $ticket->estado == 'Abierto' ? 'abierto' : 'proceso' }}">{{ $ticket->estado }}</span>
                    </div>

                    <div class="ticket-desc">
                        {{ $ticket->descripcion }}
                    </div>

                    @if($ticket->notas_equipamiento)
                        <div style="
                            background: #fffbeb;
                            border: 1px solid #fcd34d;
                            border-left: 4px solid #f59e0b;
                            border-radius: 8px;
                            padding: 0.875rem 1rem;
                            margin-bottom: 1.25rem;
                            display: flex;
                            gap: 10px;
                            align-items: flex-start;
                        ">
                            <div style="flex-shrink: 0; margin-top: 2px;">
                                <svg style="width:18px;height:18px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size:0.8rem;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                                    ⚠️ Equipamiento Necesario — Llevar Antes de Salir
                                </div>
                                <div style="font-size:0.9rem;color:#78350f;line-height:1.5;">
                                    {{ $ticket->notas_equipamiento }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($ticket->ia_resumen || $ticket->ia_categoria)
                        <div class="ia-box">
                            <div class="ia-icon">
                                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="ia-content">
                                <div class="ia-title">
                                    <span>Análisis IA: {{ $ticket->ia_categoria }}</span>
                                    <span style="color: {{ $ticket->ia_prioridad == 'Alta' ? 'var(--danger)' : ($ticket->ia_prioridad == 'Media' ? 'var(--warning)' : 'var(--success)') }}">
                                        Prioridad {{ $ticket->ia_prioridad }}
                                    </span>
                                </div>
                                <div class="ia-summary">{{ $ticket->ia_resumen }}</div>
                            </div>
                        </div>
                    @endif

                    @if($ticket->estado === 'Abierto')
                        <form action="{{ route('tecnico.ticket.status', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline">
                                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Iniciar Trabajo
                            </button>
                        </form>
                    @elseif($ticket->estado === 'En Proceso')
                        <button type="button" class="btn btn-primary" onclick="toggleResolveForm({{ $ticket->id }})">
                            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>Pulsa para tomar foto o elegir archivo</span>
                                        </div>
                                        <input type="file" name="evidencia" accept="image/*" capture="environment" required onchange="updateFileName(this, {{ $ticket->id }})">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Nota del Trabajo (Opcional)</label>
                                    <textarea name="nota_tecnico" class="form-control" rows="3" placeholder="Describe brevemente la solución aplicada..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Ubicación GPS *</label>
                                    <div id="map-{{ $ticket->id }}" class="map-container"></div>
                                    <div id="gps-status-{{ $ticket->id }}" class="gps-status">
                                        <svg style="width:16px;height:16px;animation: spin 2s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Buscando señal GPS para validar trabajo...
                                    </div>
                                    <input type="hidden" name="latitud" id="lat-{{ $ticket->id }}">
                                    <input type="hidden" name="longitud" id="lng-{{ $ticket->id }}">
                                </div>

                                <button type="submit" class="btn btn-success" id="submit-btn-{{ $ticket->id }}" disabled>
                                    Completar y Guardar
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
                    <svg style="width:32px;height:32px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span style="color:#059669;font-weight:600;">Evidencia lista (${(input.files[0].size / 1024 / 1024).toFixed(2)} MB)</span>
                `;
                label.style.borderColor = '#10b981';
                label.style.background = '#ecfdf5';
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

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    map.setView([lat, lng], 16);

                    if (marker) map.removeLayer(marker);

                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup("Ubicación exacta de trabajo").openPopup();

                    L.circle([lat, lng], { radius: accuracy, color: '#2563eb', fillOpacity: 0.1 }).addTo(map);

                    document.getElementById('lat-' + ticketId).value = lat;
                    document.getElementById('lng-' + ticketId).value = lng;

                    const statusEl = document.getElementById('gps-status-' + ticketId);
                    statusEl.className = 'gps-status locked';
                    statusEl.innerHTML = `
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Coordenadas validadas correctamente
                    `;

                    document.getElementById('submit-btn-' + ticketId).disabled = false;

                }, function(error) {
                    const statusEl = document.getElementById('gps-status-' + ticketId);
                    statusEl.innerHTML = `
                        <svg style="width:16px;height:16px;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span style="color:#ef4444;">No se pudo verificar tu ubicación. Permite el uso del GPS en el navegador.</span>
                    `;
                    // Permitir envío aun si falla la geolocalización (se recomienda usar GPS)
                    document.getElementById('submit-btn-' + ticketId).disabled = false;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                const statusEl = document.getElementById('gps-status-' + ticketId);
                statusEl.innerHTML = "Navegador incompatible con GPS. Puedes completar sin coordenadas.";
                document.getElementById('submit-btn-' + ticketId).disabled = false;
            }
        }

        function validateForm(ticketId) {
            const lat = document.getElementById('lat-' + ticketId).value;
            const lng = document.getElementById('lng-' + ticketId).value;

            if (!lat || !lng) {
                alert('Es un requisito indispensable registrar tus coordenadas GPS para validar la resolución.');
                return false;
            }

            document.getElementById('submit-btn-' + ticketId).innerHTML = 'Procesando y Guardando...';
            document.getElementById('submit-btn-' + ticketId).disabled = true;
            return true;
        }
    </script>
</body>
</html>
