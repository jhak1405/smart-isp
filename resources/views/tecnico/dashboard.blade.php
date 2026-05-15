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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/tecnico.css') }}?v={{ time() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <header>
        <div class="header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                </path>
            </svg>
            Smart ISP Tech
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            @include('components.notification-bell')
            <form method="POST" action="/logout" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn"
                    style="background:none;border:none;cursor:pointer;font-family:inherit;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Salir
                </button>
            </form>
        </div>
    </header>

    {{-- Greeting --}}
    <div style="max-width:800px;margin:0 auto;padding:1.5rem 1.25rem 0;">
        <p style="font-size:0.8rem;color:#475569;margin:0 0 2px;">Bienvenido de nuevo</p>
        <h2 style="font-size:1.3rem;font-weight:700;color:#f1f5f9;margin:0;">{{ Auth::user()->name }}</h2>
    </div>

    {{-- Minimal Text Tabs (Figma style) --}}
    <div style="max-width:800px;margin:0 auto;padding:1.25rem 1.25rem 0;">
        <div style="display:flex;gap:1.75rem;border-bottom:1px solid #1e3a5f;">
            <button id="tab-btn-tickets" onclick="switchTab('tickets')"
                class="tab-btn active">
                Mis Tickets
                @if($tickets->count() > 0)
                    <span style="background:#3b82f6;color:#fff;font-size:0.65rem;font-weight:700;padding:1px 6px;border-radius:999px;margin-left:5px;vertical-align:middle;">{{ $tickets->count() }}</span>
                @endif
            </button>
            <button id="tab-btn-pendientes" onclick="switchTab('pendientes')"
                class="tab-btn">
                Mis Pendientes
                @if($pendientes->count() > 0)
                    <span class="tab-badge">{{ $pendientes->count() }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- Global Alerts --}}
    @if($errors->any() || session('success'))
    <div style="max-width:800px;margin:1rem auto 0;padding:0 1.25rem;">
        @if($errors->any())
            <div class="alert" style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;">
                <ul style="margin:0;padding-left:1.25rem;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
    </div>
    @endif

    {{-- ====================== TAB: TICKETS ====================== --}}
    <div id="tab-tickets" class="container">
        @if($tickets->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
                <h3>Estás al día</h3>
                <p>No tienes tickets asignados por ahora.</p>
            </div>
        @else
            @foreach($tickets as $ticket)
                @php
                    $prioClass = 'prioridad-baja';
                    $badgeClass = 'badge-baja';
                    $prioLabel = 'BAJO';
                    if ($ticket->ia_prioridad == 'Alta') {
                        $prioClass = 'prioridad-alta';
                        $badgeClass = 'badge-alta';
                        $prioLabel = 'URGENTE';
                    } elseif ($ticket->ia_prioridad == 'Media') {
                        $prioClass = 'prioridad-media';
                        $badgeClass = 'badge-media';
                        $prioLabel = 'MEDIO';
                    }
                @endphp
                <div class="ticket-card {{ $prioClass }}">
                    {{-- TOP ROW: Ticket ID & Priority Badge --}}
                    <div class="ticket-top-row">
                        <div class="ticket-id">
                            ISP-{{ $ticket->id }}
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="badge {{ $badgeClass }}">
                            {{ $prioLabel }}
                        </span>
                    </div>

                    {{-- CLIENT & PROBLEM TITLE --}}
                    <div class="ticket-client-section">
                        <h2 class="ticket-client-name">
                            {{ $ticket->cliente ? $ticket->cliente->nombre_completo : 'Cliente Desconocido' }}
                        </h2>
                        <p class="ticket-problem-title">
                            {{ $ticket->titulo }}
                        </p>
                    </div>

                    {{-- DETAILS: LOCATION & TIME --}}
                    <div class="ticket-details">
                        @if($ticket->cliente && $ticket->cliente->direccion_escrita)
                        <div class="detail-item">
                            <svg style="width:18px;height:18px;color:#cbd5e1;margin-top:3px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div class="detail-item-content">
                                <span class="detail-main-text">{{ $ticket->cliente->direccion_escrita }}</span>
                                <span class="detail-sub-text">Smart ISP Zone</span>
                            </div>
                        </div>
                        @endif

                        <div class="detail-item-center">
                            <svg style="width:18px;height:18px;color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="detail-main-text">
                                {{ $ticket->created_at->format('h:i A') }} - Asignación
                            </span>
                        </div>
                        
                        @if($ticket->descripcion)
                        <div class="detail-item">
                            <svg style="width:18px;height:18px;color:#cbd5e1;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            <span class="detail-desc-text">
                                {{ $ticket->descripcion }}
                            </span>
                        </div>
                        @endif

                        @if($ticket->notas_equipamiento)
                        <div class="detail-item">
                            <svg style="width:18px;height:18px;color:#d97706;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                            </svg>
                            <div class="detail-item-content">
                                <span class="equipment-title">Equipamiento</span>
                                <span class="equipment-text">{{ $ticket->notas_equipamiento }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="ticket-divider"></div>

                    {{-- ACTIONS --}}
                    @if($ticket->estado === 'Abierto')
                        <form action="{{ route('tecnico.ticket.status', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="ticket-actions">
                                <button type="submit" class="btn-start" >
                                    Start Service
                                </button>
                                <div class="btn-circle" onclick="this.closest('form').submit()">
                                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>
                    @elseif($ticket->estado === 'En Proceso')
                        @php
                            $hasGps = $ticket->cliente && $ticket->cliente->latitud && $ticket->cliente->longitud;
                            $cLat = $hasGps ? $ticket->cliente->latitud : 'null';
                            $cLng = $hasGps ? $ticket->cliente->longitud : 'null';
                        @endphp
                        <button type="button" class="btn btn-primary" onclick="toggleResolveForm({{ $ticket->id }}, {{ $cLat }}, {{ $cLng }})">
                            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Resolver Ticket
                        </button>

                        <!-- Formulario de Resolución -->
                        <div id="resolve-form-{{ $ticket->id }}" class="resolution-form">
                            <form action="{{ route('tecnico.ticket.resolver', $ticket->id) }}" method="POST"
                                enctype="multipart/form-data" onsubmit="return validateForm({{ $ticket->id }})">
                                @csrf

                                <!-- 1. Foto de Fachada del Cliente -->
                                <div class="form-group">
                                    @if($ticket->cliente && $ticket->cliente->foto_fachada)
                                        <label class="form-label">Fachada del Cliente Registrada</label>
                                        <div style="margin-bottom: 1rem; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); background: #f8fafc; cursor: zoom-in;"
                                            onclick="openLightbox('{{ Storage::url($ticket->cliente->foto_fachada) }}')">
                                            <img src="{{ Storage::url($ticket->cliente->foto_fachada) }}" alt="Fachada del cliente"
                                                style="width: 100%; height: auto; max-height: 400px; object-fit: contain; display: block;">
                                            <div
                                                style="background: var(--bg-card); padding: 8px; font-size: 0.8rem; color: var(--text-secondary); text-align: center;">
                                                📷 Toca la foto para ampliar
                                            </div>
                                        </div>
                                    @else
                                        <label class="form-label" style="color: var(--danger);">Foto de Fachada del Cliente *</label>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 8px;">Este cliente
                                            es nuevo o no tiene fachada. Por favor tómale una foto a la casa.</div>
                                        <div class="file-upload">
                                            <div class="file-upload-label" id="file-fachada-label-{{ $ticket->id }}">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18">
                                                    </path>
                                                </svg>
                                                <span>Tomar foto de la fachada</span>
                                            </div>
                                            <input type="file" name="foto_fachada" accept="image/*" capture="environment" required
                                                onchange="updateFachadaName(this, {{ $ticket->id }})">
                                        </div>
                                    @endif
                                </div>

                                <!-- 2. Evidencia del Trabajo -->
                                <div class="form-group">
                                    <label class="form-label">Evidencia del Trabajo Realizado *</label>
                                    <div class="file-upload">
                                        <div class="file-upload-label" id="file-label-{{ $ticket->id }}">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Pulsa para tomar foto del equipo/trabajo</span>
                                        </div>
                                        <input type="file" name="evidencia" accept="image/*" capture="environment" required
                                            onchange="updateFileName(this, {{ $ticket->id }})">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Nota del Trabajo (Opcional)</label>
                                    <textarea name="nota_tecnico" class="form-control" rows="3"
                                        placeholder="Describe brevemente la solución aplicada..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Ubicación GPS *</label>
                                    <div id="map-{{ $ticket->id }}" class="map-container"></div>
                                    <div id="gps-status-{{ $ticket->id }}" class="gps-status {{ $hasGps ? 'locked' : '' }}">
                                        @if($hasGps)
                                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Ubicación del cliente cargada correctamente.
                                        @else
                                            <svg style="width:16px;height:16px;animation: spin 2s linear infinite;" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                </path>
                                            </svg>
                                            Buscando señal GPS para validar trabajo...
                                        @endif
                                    </div>
                                    @if($hasGps)
                                    <button type="button" class="btn-outline" style="width:100%; margin-top:8px; padding:0.5rem; font-size:0.85rem; border-radius:8px;" onclick="updateGpsManual({{ $ticket->id }})">
                                        <svg style="width:14px;height:14px;margin-right:4px;display:inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        Actualizar a mi ubicación actual (Si se mudó)
                                    </button>
                                    @endif
                                    <input type="hidden" name="latitud" id="lat-{{ $ticket->id }}" value="{{ $hasGps ? $ticket->cliente->latitud : '' }}">
                                    <input type="hidden" name="longitud" id="lng-{{ $ticket->id }}" value="{{ $hasGps ? $ticket->cliente->longitud : '' }}">
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
    </div>{{-- end #tab-tickets --}}

    {{-- ====================== TAB: PENDIENTES ====================== --}}
    <div id="tab-pendientes" class="container" style="display:none;">
        @if($pendientes->isEmpty())
            <div
                style="background:var(--bg-card);border:1px dashed var(--border-color);border-radius:12px;padding:2rem;text-align:center;color:var(--text-muted);">
                <svg style="width:36px;height:36px;margin:0 auto 0.5rem;display:block;opacity:0.4;" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p style="font-size:0.9rem;margin:0;">No tienes pendientes asignados por ahora.</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                @foreach($pendientes as $pendiente)
                    @php
                        $fechaHoy = now()->startOfDay();
                        $fechaPend = $pendiente->fecha_recordatorio;
                        $esVencido = $fechaPend->lt($fechaHoy);
                        $esHoy = $fechaPend->isToday();

                        // Glowing Dark Mode Colors
                        $borderColor = $esVencido ? 'rgba(239, 68, 68, 0.5)' : ($esHoy ? 'rgba(245, 158, 11, 0.5)' : 'rgba(59, 130, 246, 0.3)');
                        $solidColor = $esVencido ? '#ef4444' : ($esHoy ? '#f59e0b' : '#3b82f6');
                        $glowBox = $esVencido ? 'box-shadow: -6px 0 15px -8px rgba(239, 68, 68, 0.5);' : ($esHoy ? 'box-shadow: -6px 0 15px -8px rgba(245, 158, 11, 0.5);' : '');
                        
                        $badgeBg = $esVencido ? 'rgba(239, 68, 68, 0.15)' : ($esHoy ? 'rgba(245, 158, 11, 0.15)' : 'rgba(59, 130, 246, 0.1)');
                        $badgeColor = $esVencido ? '#fca5a5' : ($esHoy ? '#fcd34d' : '#94a3b8');
                        $badgeText = $esVencido ? 'Vencido' : ($esHoy ? 'Hoy' : $fechaPend->translatedFormat('d M Y'));
                    @endphp

                    <div
                        class="pendiente-card" style="border-left:3px solid {{ $solidColor }}; {{ $glowBox }}">

                        {{-- Ícono genérico --}}
                        <div class="pendiente-icon">
                            <svg style="width:20px;height:20px;color:#64748b;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>

                        {{-- Contenido --}}
                        <div class="pendiente-content">
                            <div class="pendiente-header">
                                <span class="pendiente-client">
                                    {{ $pendiente->cliente ? $pendiente->cliente->nombre_completo : 'Cliente no asignado' }}
                                </span>
                                @if($pendiente->tipo)
                                    <span class="pendiente-tag">{{ $pendiente->tipo }}</span>
                                @endif
                                <span class="pendiente-tag" style="background:{{ $badgeBg }};color:{{ $badgeColor }};">{{ $badgeText }}</span>
                            </div>

                            @if($pendiente->descripcion)
                                <p class="pendiente-desc">
                                    {{ $pendiente->descripcion }}</p>
                            @endif

                            <div class="pendiente-footer">
                                @if($pendiente->cliente && $pendiente->cliente->direccion_escrita)
                                    <span class="pendiente-footer-item">
                                        <svg style="width:14px;height:14px;color:#475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $pendiente->cliente->direccion_escrita }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Botón Completar --}}
                        <form method="POST" action="{{ route('tecnico.pendiente.completar', $pendiente->id) }}" style="flex-shrink:0;">
                            @csrf
                            <button type="submit"
                                class="btn-complete">
                                Completar
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>{{-- end #tab-pendientes --}}

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Animations
        const style = document.createElement('style');
        style.innerHTML = `@keyframes spin { 100% { transform: rotate(360deg); } }`;
        document.head.appendChild(style);

        // Tab switching logic
        function switchTab(tab) {
            const ticketsTab = document.getElementById('tab-tickets');
            const pendientesTab = document.getElementById('tab-pendientes');
            const btnTickets = document.getElementById('tab-btn-tickets');
            const btnPendientes = document.getElementById('tab-btn-pendientes');

            if (tab === 'tickets') {
                ticketsTab.style.display = 'block';
                pendientesTab.style.display = 'none';
                btnTickets.style.color = '#f1f5f9';
                btnTickets.style.borderBottomColor = '#3b82f6';
                btnPendientes.style.color = '#475569';
                btnPendientes.style.borderBottomColor = 'transparent';
            } else {
                ticketsTab.style.display = 'none';
                pendientesTab.style.display = 'block';
                btnPendientes.style.color = '#f1f5f9';
                btnPendientes.style.borderBottomColor = '#3b82f6';
                btnTickets.style.color = '#475569';
                btnTickets.style.borderBottomColor = 'transparent';
            }
        }

        function toggleResolveForm(ticketId, lat = null, lng = null) {
            const form = document.getElementById('resolve-form-' + ticketId);
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                form.style.display = 'block';
                initMap(ticketId, lat, lng);
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

        function updateFachadaName(input, ticketId) {
            const label = document.getElementById('file-fachada-label-' + ticketId);
            if (input.files && input.files.length > 0) {
                label.innerHTML = `
                    <svg style="width:32px;height:32px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span style="color:#059669;font-weight:600;">Fachada lista (${(input.files[0].size / 1024 / 1024).toFixed(2)} MB)</span>
                `;
                label.style.borderColor = '#10b981';
                label.style.background = '#ecfdf5';
            }
        }

        let maps = {};

        function initMap(ticketId, cLat = null, cLng = null) {
            if (maps[ticketId]) return; // Evitar reinicializar

            // Crear mapa
            let initialLat = cLat !== null ? cLat : -5.19449;
            let initialLng = cLng !== null ? cLng : -80.63282;

            const map = L.map('map-' + ticketId).setView([initialLat, initialLng], cLat ? 16 : 13);
            maps[ticketId] = map;

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            if (cLat !== null && cLng !== null) {
                L.marker([cLat, cLng]).addTo(map).bindPopup("Ubicación Registrada").openPopup();
                document.getElementById('submit-btn-' + ticketId).disabled = false;
            } else {
                updateGpsManual(ticketId);
            }
        }

        function updateGpsManual(ticketId) {
            const map = maps[ticketId];
            if (!map) return;
            
            const statusEl = document.getElementById('gps-status-' + ticketId);
            statusEl.className = 'gps-status';
            statusEl.innerHTML = `
                <svg style="width:16px;height:16px;animation: spin 2s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Buscando señal GPS para validar trabajo...
            `;

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    map.setView([lat, lng], 16);

                    // clear previous markers if we had them saved, though Leaflet doesn't track them easily without a reference.
                    // Instead we just add the new one.
                    L.marker([lat, lng]).addTo(map)
                        .bindPopup("Tu ubicación actual (Actualizada)").openPopup();

                    L.circle([lat, lng], { radius: accuracy, color: '#2563eb', fillOpacity: 0.1 }).addTo(map);

                    document.getElementById('lat-' + ticketId).value = lat;
                    document.getElementById('lng-' + ticketId).value = lng;

                    statusEl.className = 'gps-status locked';
                    statusEl.innerHTML = `
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Coordenadas validadas correctamente
                    `;

                    document.getElementById('submit-btn-' + ticketId).disabled = false;

                }, function (error) {
                    statusEl.innerHTML = `
                        <svg style="width:16px;height:16px;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span style="color:#ef4444;">No se pudo verificar tu ubicación. Permite el uso del GPS en el navegador.</span>
                    `;
                    document.getElementById('submit-btn-' + ticketId).disabled = false;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
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

{{-- Lightbox para ver la foto de fachada a pantalla completa --}}
<div id="lightbox-overlay" onclick="closeLightbox()" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92);
            justify-content:center; align-items:center; flex-direction:column; cursor:zoom-out;">
    <img id="lightbox-img" src="" alt="Foto ampliada"
        style="max-width:95vw; max-height:88vh; object-fit:contain; border-radius:8px; box-shadow:0 8px 40px rgba(0,0,0,0.6);">
    <p style="color:#aaa; font-size:0.8rem; margin-top:12px;">Toca en cualquier lugar para cerrar</p>
</div>

<script>
    function openLightbox(url) {
        document.getElementById('lightbox-img').src = url;
        const overlay = document.getElementById('lightbox-overlay');
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }
    // Cerrar también con la tecla ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>

</html>