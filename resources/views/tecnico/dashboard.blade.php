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
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #263348;
            --border-color: #334155;
            --text-main: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #475569;
            --success: #10b981;
            --success-bg: rgba(16,185,129,0.12);
            --warning: #f59e0b;
            --warning-bg: rgba(245,158,11,0.12);
            --danger: #ef4444;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.4);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.5);
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
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-title svg { color: #3b82f6; width: 22px; height: 22px; }

        .logout-btn {
            color: #64748b;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }
        .logout-btn:hover { color: #ef4444; }

        /* Container */
        .container {
            padding: 1.25rem 1.25rem;
            max-width: 800px;
            margin: 0 auto;
            padding-bottom: 80px;
        }

        /* Cards */
        /* Cards (Glassmorphism & Glowing Borders) */
        .ticket-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .ticket-card:hover { 
            background: rgba(30, 41, 59, 0.7); 
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Glowing priority borders */
        .ticket-card.en-proceso { 
            border-left: 4px solid var(--warning); 
            box-shadow: -8px 0 25px -10px rgba(245, 158, 11, 0.5);
        }
        
        .ticket-card.abierto { 
            border-left: 4px solid var(--primary); 
            box-shadow: -8px 0 25px -10px rgba(59, 130, 246, 0.5);
        }

        /* If high priority/urgent, make it red glow (Example) */
        .ticket-card.urgente { 
            border-left: 4px solid var(--danger); 
            box-shadow: -8px 0 25px -10px rgba(239, 68, 68, 0.6);
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .ticket-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #f8fafc;
            margin-bottom: 0.4rem;
            line-height: 1.4;
            letter-spacing: -0.01em;
        }

        .ticket-client {
            font-size: 0.9rem;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ticket-client svg { color: #64748b; }

        /* Figma Style Badges */
        .badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .badge.abierto {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .badge.proceso {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .ticket-desc {
            font-size: 0.95rem;
            color: #94a3b8;
            margin-bottom: 1.25rem;
            line-height: 1.6;
            background: rgba(15, 23, 42, 0.4);
            padding: 1rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .ia-box {
            background: linear-gradient(145deg, rgba(30,41,59,0.5) 0%, rgba(15,23,42,0.5) 100%);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(59, 130, 246, 0.2);
            display: flex;
            gap: 12px;
            align-items: flex-start;
            box-shadow: inset 0 2px 10px rgba(59, 130, 246, 0.05);
        }

        .ia-icon {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            padding: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
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
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            gap: 8px;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 15px -3px rgba(59, 130, 246, 0.5);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px -3px rgba(59, 130, 246, 0.7);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px -3px rgba(16, 185, 129, 0.5);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .btn-success:hover {
            box-shadow: 0 6px 20px -3px rgba(16, 185, 129, 0.7);
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255,255,255,0.1);
            color: #e2e8f0;
            backdrop-filter: blur(4px);
        }

        .btn-outline:hover {
            background-color: rgba(51, 65, 85, 0.8);
            border-color: rgba(255,255,255,0.2);
        }

        /* Forms */
        .resolution-form {
            display: none;
            animation: fadeIn 0.3s ease-out forwards;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group { margin-bottom: 1.25rem; }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #cbd5e1;
            letter-spacing: 0.3px;
        }

        .form-control {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s;
            resize: vertical;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: rgba(30, 41, 59, 0.8);
        }

        .form-control::placeholder { color: #64748b; }

        /* Custom File Input */
        .file-upload {
            position: relative;
            display: block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0; top: 0; opacity: 0;
            width: 100%; height: 100%;
            cursor: pointer; z-index: 10;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.4);
            border: 2px dashed rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .file-upload-label svg {
            color: #64748b;
            width: 32px; height: 32px;
            transition: all 0.2s;
        }

        .file-upload:hover .file-upload-label {
            background: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
            color: #60a5fa;
            box-shadow: inset 0 0 15px rgba(59, 130, 246, 0.1);
        }

        .file-upload:hover .file-upload-label svg { color: #3b82f6; }

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
                style="background:none;border:none;padding:0 0 10px;font-family:'Inter',sans-serif;font-size:0.95rem;font-weight:600;color:#f1f5f9;cursor:pointer;border-bottom:2px solid #3b82f6;margin-bottom:-1px;transition:all 0.2s;">
                Mis Tickets
                @if($tickets->count() > 0)
                    <span style="background:#3b82f6;color:#fff;font-size:0.65rem;font-weight:700;padding:1px 6px;border-radius:999px;margin-left:5px;vertical-align:middle;">{{ $tickets->count() }}</span>
                @endif
            </button>
            <button id="tab-btn-pendientes" onclick="switchTab('pendientes')"
                style="background:none;border:none;padding:0 0 10px;font-family:'Inter',sans-serif;font-size:0.95rem;font-weight:600;color:#475569;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:all 0.2s;">
                Mis Pendientes
                @if($pendientes->count() > 0)
                    <span style="background:#334155;color:#94a3b8;font-size:0.65rem;font-weight:700;padding:1px 6px;border-radius:999px;margin-left:5px;vertical-align:middle;">{{ $pendientes->count() }}</span>
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
                <div class="ticket-card {{ $ticket->estado == 'En Proceso' ? 'en-proceso' : 'abierto' }}">
                    {{-- TOP ROW: Ticket ID & Priority Badge --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                        <div style="display:flex; align-items:center; gap:6px; color:#64748b; font-size:0.8rem; font-weight:600; letter-spacing:0.5px;">
                            ISP-{{ $ticket->id }}
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="badge {{ $ticket->estado == 'Abierto' ? 'abierto' : 'proceso' }}" style="border-radius:999px; font-size:0.65rem; border:1px solid currentColor;">
                            {{ $ticket->estado == 'Abierto' ? 'URGENTE' : 'EN CURSO' }}
                        </span>
                    </div>

                    {{-- CLIENT & PROBLEM TITLE --}}
                    <div style="margin-bottom:1.25rem;">
                        <h2 style="font-size:1.35rem; font-weight:700; color:#ffffff; margin-bottom:0.2rem; letter-spacing:-0.02em;">
                            {{ $ticket->cliente ? $ticket->cliente->nombre_completo : 'Cliente Desconocido' }}
                        </h2>
                        <p style="font-size:1rem; color:#94a3b8; margin:0;">
                            {{ $ticket->titulo }}
                        </p>
                    </div>

                    {{-- DETAILS: LOCATION & TIME --}}
                    <div style="display:flex; flex-direction:column; gap:0.85rem; margin-bottom:1.5rem;">
                        @if($ticket->cliente && $ticket->cliente->direccion_escrita)
                        <div style="display:flex; align-items:flex-start; gap:12px;">
                            <svg style="width:18px;height:18px;color:#cbd5e1;margin-top:3px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div style="display:flex; flex-direction:column; gap:2px;">
                                <span style="font-size:0.95rem; color:#f1f5f9; font-weight:500;">{{ $ticket->cliente->direccion_escrita }}</span>
                                <span style="font-size:0.85rem; color:#64748b;">Smart ISP Zone</span>
                            </div>
                        </div>
                        @endif

                        <div style="display:flex; align-items:center; gap:12px;">
                            <svg style="width:18px;height:18px;color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span style="font-size:0.95rem; color:#f1f5f9; font-weight:500;">
                                {{ $ticket->created_at->format('h:i A') }} - Asignación
                            </span>
                        </div>
                        
                        @if($ticket->descripcion)
                        <div style="display:flex; align-items:flex-start; gap:12px; margin-top:0.25rem;">
                            <svg style="width:18px;height:18px;color:#cbd5e1;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            <span style="font-size:0.9rem; color:#94a3b8; line-height:1.5;">
                                {{ $ticket->descripcion }}
                            </span>
                        </div>
                        @endif

                        @if($ticket->notas_equipamiento)
                        <div style="display:flex; align-items:flex-start; gap:12px; margin-top:0.25rem;">
                            <svg style="width:18px;height:18px;color:#d97706;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                            </svg>
                            <div style="display:flex; flex-direction:column; gap:2px;">
                                <span style="font-size:0.6rem; color:#d97706; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Equipamiento</span>
                                <span style="font-size:0.9rem; color:#cbd5e1; line-height:1.4;">{{ $ticket->notas_equipamiento }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div style="border-top:1px solid rgba(255,255,255,0.05); margin-bottom:1.25rem;"></div>

                    {{-- ACTIONS --}}
                    @if($ticket->estado === 'Abierto')
                        <form action="{{ route('tecnico.ticket.status', $ticket->id) }}" method="POST">
                            @csrf
                            <div style="display:flex; gap:10px;">
                                <button type="submit" class="btn-primary" style="flex:1; border-radius:999px; padding:0.9rem; font-size:1rem; border:none; box-shadow:none;">
                                    Start Service
                                </button>
                                <div style="width:50px; height:50px; border-radius:50%; background:#1e293b; border:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; color:#94a3b8; cursor:pointer;" onclick="this.closest('form').submit()">
                                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>
                    @elseif($ticket->estado === 'En Proceso')
                        <button type="button" class="btn btn-primary" onclick="toggleResolveForm({{ $ticket->id }})">
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
                                    <div id="gps-status-{{ $ticket->id }}" class="gps-status">
                                        <svg style="width:16px;height:16px;animation: spin 2s linear infinite;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
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
                        style="background:rgba(30, 41, 59, 0.4);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.05);border-left:3px solid {{ $solidColor }};border-radius:14px;padding:1.25rem;display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;transition:all 0.3s;{{ $glowBox }}"
                        onmouseover="this.style.background='rgba(30, 41, 59, 0.7)';this.style.borderColor='rgba(255,255,255,0.1)';" onmouseout="this.style.background='rgba(30, 41, 59, 0.4)';this.style.borderColor='rgba(255,255,255,0.05)';">

                        {{-- Ícono genérico --}}
                        <div style="flex-shrink:0;margin-top:2px;">
                            <svg style="width:20px;height:20px;color:#64748b;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>

                        {{-- Contenido --}}
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                                <span style="font-weight:600;font-size:1.05rem;color:#f8fafc;letter-spacing:-0.01em;">
                                    {{ $pendiente->cliente ? $pendiente->cliente->nombre_completo : 'Cliente no asignado' }}
                                </span>
                                @if($pendiente->tipo)
                                    <span style="background:rgba(51, 65, 85, 0.5);color:#cbd5e1;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:6px;border:1px solid rgba(255,255,255,0.05);">{{ $pendiente->tipo }}</span>
                                @endif
                                <span style="background:{{ $badgeBg }};color:{{ $badgeColor }};font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:6px;border:1px solid rgba(255,255,255,0.05);">{{ $badgeText }}</span>
                            </div>

                            @if($pendiente->descripcion)
                                <p style="font-size:0.85rem;color:#94a3b8;margin:0 0 8px;line-height:1.5;">
                                    {{ $pendiente->descripcion }}</p>
                            @endif

                            <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:0.8rem;color:#64748b;">
                                @if($pendiente->cliente && $pendiente->cliente->direccion_escrita)
                                    <span style="display:flex;align-items:center;gap:4px;">
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
                                style="background:linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);color:#34d399;border:1px solid rgba(16, 185, 129, 0.3);border-radius:8px;padding:8px 16px;font-size:0.85rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:all 0.2s;box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);"
                                onmouseover="this.style.background='linear-gradient(135deg, #10b981 0%, #059669 100%)';this.style.color='#fff';this.style.boxShadow='0 4px 15px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%)';this.style.color='#34d399';this.style.boxShadow='0 2px 10px rgba(16, 185, 129, 0.1)';">
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
                navigator.geolocation.getCurrentPosition(function (position) {
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

                }, function (error) {
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