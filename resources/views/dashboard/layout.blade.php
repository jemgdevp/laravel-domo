<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="dark light">

    <title>@yield('title', 'Dashboard') — Laravel Domo</title>

    {{-- No-FOUC theme: runs before paint, sets data-theme synchronously --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('domo-theme');
                var system = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                var theme = (stored === 'light' || stored === 'dark') ? stored : system;
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>

    {{-- Alpine.js plugins MUST load before the core (CDN, zero-build) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    {{-- Alpine.js core (CDN, zero-build) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @include('domo::dashboard.partials.tokens')

    @stack('styles')
</head>
<body
    x-data="domoShell()"
    x-init="init()"
    :class="{ 'sidebar-collapsed': collapsed, 'drawer-open': drawerOpen }"
    @keydown.window.escape="onEscape()"
    @keydown.window.tab="trapDrawer($event)"
    @domo-toast.window="pushToast($event.detail)"
    @domo-toggle-theme.window="toggleTheme()"
>
    {{-- Skip to content for keyboard users --}}
    <a href="#domo-content" class="skip-link">Skip to content</a>

    {{-- Ambient background: faint grid + radial red glow --}}
    <div class="app-bg" aria-hidden="true">
        <div class="app-bg-grid"></div>
        <div class="app-bg-glow"></div>
        <div class="app-bg-scanline"></div>
    </div>

    @include('domo::dashboard.partials.sidebar')

    {{-- Mobile drawer backdrop --}}
    <div
        class="drawer-backdrop"
        x-show="drawerOpen"
        x-transition.opacity
        @click="closeDrawer()"
        aria-hidden="true"
        x-cloak
    ></div>

    <div class="app-frame">
        @include('domo::dashboard.partials.topbar')

        <main id="domo-content" class="main" tabindex="-1">
            <div class="main-inner">
                @yield('content')
            </div>

            <footer class="footer">
                <div class="footer-inner">
                    <span class="footer-logo">
                        <span class="pixel-dot" aria-hidden="true"></span>
                        <span class="mono">laravel-domo</span>
                    </span>
                    <span class="footer-meta mono">
                        v{{ config('app.version', '0.1.0') }}
                    </span>
                    <span class="footer-credit">Made for Laravel · MIT</span>
                </div>
            </footer>
        </main>
    </div>

    @include('domo::dashboard.partials.command-palette')
    @include('domo::dashboard.partials.toasts')
    @include('domo::dashboard.partials.shell-script')

    @stack('scripts')
</body>
</html>
