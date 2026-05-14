<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Smart ISP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            background-image: radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.08) 0%, transparent 60%),
                              radial-gradient(ellipse at 80% 20%, rgba(99,102,241,0.06) 0%, transparent 60%);
        }

        .card {
            width: 100%;
            max-width: 400px;
            margin: 1.5rem;
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg { width: 22px; height: 22px; color: white; }

        .logo-text { font-size: 1.2rem; font-weight: 700; color: #f1f5f9; }
        .logo-sub  { font-size: 0.75rem; color: #64748b; font-weight: 400; }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 0.3rem;
        }

        .subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 2rem;
        }

        .field { margin-bottom: 1.25rem; }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 0.4rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.7rem 1rem;
            background: #0f172a;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #f1f5f9;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }

        input.is-error { border-color: #ef4444; }

        .error-msg {
            font-size: 0.78rem;
            color: #f87171;
            margin-top: 0.4rem;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: #64748b;
            cursor: pointer;
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #3b82f6;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
        }

        .btn-login:hover   { opacity: 0.9; }
        .btn-login:active  { transform: scale(0.99); }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            font-size: 0.78rem;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.06);
        }

        .btn-admin {
            width: 100%;
            padding: 0.65rem;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #64748b;
            font-size: 0.82rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: border-color 0.2s, color 0.2s;
        }

        .btn-admin:hover { border-color: #3b82f6; color: #93c5fd; }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">
        <div class="logo-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
            </svg>
        </div>
        <div>
            <div class="logo-text">Smart ISP</div>
            <div class="logo-sub">Sistema de Gestión</div>
        </div>
    </div>

    <h1>Bienvenido</h1>
    <p class="subtitle">Ingresa tus credenciales para continuar</p>

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="field">
            <label for="email">Correo Electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus
                class="{{ $errors->has('email') ? 'is-error' : '' }}"
                placeholder="usuario@empresa.com"
            >
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                class="{{ $errors->has('password') ? 'is-error' : '' }}"
                placeholder="••••••••"
            >
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <label class="remember">
            <input type="checkbox" name="remember" id="remember">
            Mantener sesión iniciada
        </label>

        <button type="submit" class="btn-login">Iniciar Sesión</button>
    </form>

    <div class="divider">Acceso administrativo</div>
    <a href="/admin/login" class="btn-admin">Ir al Panel de Administración</a>
</div>

</body>
</html>
