<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Laravel Domo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            /* Laravel Colors */
            --laravel-red: #FF2D20;
            --laravel-dark: #1F2937;
            --laravel-gray: #4B5563;
            --laravel-light: #F9FAFB;
            
            /* Semantic Colors */
            --color-primary: #FF2D20;
            --color-success: #10B981;
            --color-warning: #F59E0B;
            --color-error: #EF4444;
            --color-info: #3B82F6;
            
            /* UI Colors */
            --bg-primary: #FFFFFF;
            --bg-secondary: #F9FAFB;
            --bg-tertiary: #F3F4F6;
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --border-color: #E5E7EB;
            
            /* Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            
            /* Border Radius */
            --radius-sm: 0.25rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Header */
        .header {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: var(--spacing-md) 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .header-content {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--laravel-red);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .nav {
            display: flex;
            gap: var(--spacing-lg);
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-md);
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--laravel-red);
            background: var(--bg-tertiary);
        }

        .nav-link.active {
            color: var(--laravel-red);
            background: #FEF2F2;
        }

        /* Main Content */
        .main {
            max-width: 1440px;
            margin: 0 auto;
            padding: var(--spacing-xl);
            min-height: calc(100vh - 140px);
        }

        /* Cards */
        .card {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            border: 1px solid var(--border-color);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }

        .stat-card {
            background: var(--bg-primary);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: var(--spacing-xs);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-change {
            font-size: 0.75rem;
            margin-top: var(--spacing-xs);
        }

        .stat-change.positive {
            color: var(--color-success);
        }

        .stat-change.negative {
            color: var(--color-error);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--laravel-red);
            color: white;
        }

        .btn-primary:hover {
            background: #E53E30;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        .btn-sm {
            padding: var(--spacing-xs) var(--spacing-sm);
            font-size: 0.75rem;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .table th {
            background: var(--bg-tertiary);
            padding: var(--spacing-sm) var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
        }

        .table td {
            padding: var(--spacing-sm) var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }

        .table tr:hover {
            background: var(--bg-secondary);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-primary {
            background: #FEF2F2;
            color: var(--laravel-red);
        }

        .badge-success {
            background: #D1FAE5;
            color: var(--color-success);
        }

        .badge-warning {
            background: #FEF3C7;
            color: var(--color-warning);
        }

        .badge-info {
            background: #DBEAFE;
            color: var(--color-info);
        }

        /* Code */
        code {
            background: var(--bg-tertiary);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.875em;
        }

        /* Footer */
        .footer {
            background: var(--bg-primary);
            border-top: 1px solid var(--border-color);
            padding: var(--spacing-md) 0;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* Utilities */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: var(--spacing-sm); }
        .gap-4 { gap: var(--spacing-md); }
        .mb-4 { margin-bottom: var(--spacing-md); }
        .mb-6 { margin-bottom: var(--spacing-lg); }
        .text-sm { font-size: 0.875rem; }
        .text-muted { color: var(--text-secondary); }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: var(--spacing-md);
            }

            .nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .main {
                padding: var(--spacing-md);
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="{{ route('domo.index') }}" class="logo">
                <span>🏠</span>
                <span>Domo</span>
            </a>
            
            <nav class="nav">
                <a href="{{ route('domo.index') }}" class="nav-link {{ request()->routeIs('domo.index') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('domo.schema') }}" class="nav-link {{ request()->routeIs('domo.schema') ? 'active' : '' }}">
                    Schema
                </a>
                <a href="{{ route('domo.models') }}" class="nav-link {{ request()->routeIs('domo.models') ? 'active' : '' }}">
                    Models
                </a>
                <a href="{{ route('domo.analyze') }}" class="nav-link {{ request()->routeIs('domo.analyze') ? 'active' : '' }}">
                    AI
                </a>
            </nav>
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="header-content">
            <p>Laravel Domo v{{ config('app.version', '0.1.0') }} | Made with ❤️ for Laravel</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
