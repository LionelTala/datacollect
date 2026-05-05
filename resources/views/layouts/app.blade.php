<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DataCollect</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%234361ee'/%3E%3Ctext x='50' y='68' text-anchor='middle' fill='white' font-size='48' font-weight='bold' font-family='Arial'%3EDC%3C/text%3E%3C/svg%3E">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">

    @livewireStyles

    <!-- Dans <head> -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4361ee">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="DataCollect">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * { font-family: 'Inter', sans-serif; }
        body { background: var(--gray-50); }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: white;
            border-right: 1px solid var(--gray-200);
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid var(--gray-100);
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 0;
        }

        .sidebar-header p {
            font-size: 0.75rem;
            color: var(--gray-400);
            margin: 4px 0 0;
        }

        .nav-sidebar {
            padding: 20px 16px;
        }

        .nav-sidebar .nav-item { margin-bottom: 6px; }

        .nav-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: var(--gray-600);
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-sidebar .nav-link i { width: 22px; font-size: 1.1rem; }
        .nav-sidebar .nav-link:hover { background: var(--gray-100); color: var(--primary); }
        .nav-sidebar .nav-link.active { background: var(--primary-light); color: var(--primary); }
        .nav-sidebar .nav-link.active i { color: var(--primary); }

        .user-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            border-top: 1px solid var(--gray-100);
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }

        .main-content {
            margin-left: 280px;
            padding: 32px;
            min-height: 100vh;
        }

        .btn-primary-custom {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 500;
            color: white !important;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background: #3b52d4;
            transform: translateY(-1px);
            color: white !important;
        }

        .btn-outline-custom {
            border: 1px solid var(--gray-300);
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 500;
            background: white;
            color: var(--gray-700);
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-outline-custom:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
            color: var(--gray-800);
        }

        .card-modern {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            transition: all 0.2s;
        }

        .card-modern:hover {
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        .form-control, .form-select {
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 10px 16px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .badge-modern {
            padding: 6px 12px;
            border-radius: 100px;
            font-weight: 500;
            font-size: 0.7rem;
        }

        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 1;
        }

        .border-dashed {
            border: 2px dashed #cbd5e1;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle {
                display: block;
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 1100;
                background: white;
                border: 1px solid var(--gray-200);
                border-radius: 10px;
                padding: 8px 12px;
            }
        }

        @media (min-width: 769px) { .menu-toggle { display: none; } }
    </style>
</head>
<body>
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>DataCollect</h2>
            <p>Collaborative Data Platform</p>
        </div>

        <ul class="nav-sidebar nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('collectes.list') }}" class="nav-link {{ request()->routeIs('collectes.list') ? 'active' : '' }}">
                    <i class="fas fa-folder-open"></i>
                    <span>Mes collectes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('collecte.create') }}" class="nav-link {{ request()->routeIs('collecte.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Créer une collecte</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Mon profil</span>
                </a>
            </li>
        </ul>

        <div class="user-info">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold small">{{ Auth::user()->name }}</div>
                    <div class="text-muted small">{{ Auth::user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm text-muted" style="background: none; border: none;">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content">
        {{ $slot }}
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
    </script>
</body>
</html>
