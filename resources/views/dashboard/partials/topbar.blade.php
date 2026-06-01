{{-- Sticky, backdrop-blur topbar. --}}
<header class="topbar">
    {{-- Mobile hamburger opens the drawer --}}
    <button type="button" class="btn-icon hamburger" @click="openDrawer()"
            aria-label="Open navigation menu" :aria-expanded="drawerOpen ? 'true' : 'false'" aria-controls="domo-sidebar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    {{-- Mobile-only logo (sidebar is hidden off-canvas) --}}
    <a href="{{ route('domo.index') }}" class="topbar-mobile-logo" aria-label="Laravel Domo home">
        <span class="pixel-dot" aria-hidden="true"></span>
        <span>DOMO</span>
    </a>

    <div class="topbar-spacer"></div>

    {{-- Command palette trigger --}}
    <button type="button" class="palette-trigger" @click="openPalette()" aria-label="Open command palette" aria-keyshortcuts="Meta+K Control+K">
        <svg class="pt-icon" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span class="pt-label">Search & commands…</span>
        <kbd class="kbd">⌘K</kbd>
    </button>

    {{-- Theme toggle --}}
    <button type="button" class="btn-icon is-bordered" @click="toggleTheme()"
            :aria-label="theme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme'"
            :aria-pressed="theme === 'light' ? 'true' : 'false'" title="Toggle theme">
        {{-- Sun (shown in dark mode) --}}
        <svg x-show="theme === 'dark'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
        {{-- Moon (shown in light mode) --}}
        <svg x-show="theme === 'light'" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
    </button>
</header>
