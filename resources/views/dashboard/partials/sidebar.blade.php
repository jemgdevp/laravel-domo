{{-- Fixed sidebar on desktop, accessible slide-in drawer on mobile. --}}
<aside
    class="sidebar"
    id="domo-sidebar"
    x-ref="sidebar"
    aria-label="Navigation menu"
    :role="(isMobile && drawerOpen) ? 'dialog' : null"
    :aria-modal="(isMobile && drawerOpen) ? 'true' : null"
    :aria-hidden="(isMobile && !drawerOpen) ? 'true' : 'false'"
    :inert="(isMobile && !drawerOpen) ? true : null"
>
    <div class="sidebar-head">
        <a href="{{ route('domo.index') }}" class="sidebar-brand" aria-label="Laravel Domo home">
            <span class="pixel-dot" aria-hidden="true"></span>
            <span class="sidebar-brand-text">DOMO</span>
        </a>
    </div>

    <nav class="sidebar-nav" aria-label="Dashboard sections">
        <span class="nav-section-label">Workspace</span>

        <a href="{{ route('domo.index') }}"
           class="nav-item {{ request()->routeIs('domo.index') ? 'is-active' : '' }}"
           @if(request()->routeIs('domo.index')) aria-current="page" @endif
           @click="closeDrawer()">
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
            </span>
            <span class="nav-label">Dashboard</span>
        </a>

        <a href="{{ route('domo.schema') }}"
           class="nav-item {{ request()->routeIs('domo.schema') ? 'is-active' : '' }}"
           @if(request()->routeIs('domo.schema')) aria-current="page" @endif
           @click="closeDrawer()">
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>
            </span>
            <span class="nav-label">Schema</span>
        </a>

        <a href="{{ route('domo.models') }}"
           class="nav-item {{ request()->routeIs('domo.models') ? 'is-active' : '' }}"
           @if(request()->routeIs('domo.models')) aria-current="page" @endif
           @click="closeDrawer()">
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            </span>
            <span class="nav-label">Models</span>
        </a>

        <a href="{{ route('domo.analyze') }}"
           class="nav-item {{ request()->routeIs('domo.analyze') ? 'is-active' : '' }}"
           @if(request()->routeIs('domo.analyze')) aria-current="page" @endif
           @click="closeDrawer()">
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a3 3 0 0 0-3 3 3 3 0 0 0-3 3 3 3 0 0 0 0 6 3 3 0 0 0 3 3 3 3 0 0 0 6 0 3 3 0 0 0 3-3 3 3 0 0 0 0-6 3 3 0 0 0-3-3 3 3 0 0 0-3-3Z"/><path d="M12 3v18M6 9h.01M18 9h.01M6 15h.01M18 15h.01"/></svg>
            </span>
            <span class="nav-label">AI Analysis</span>
        </a>
    </nav>

    <div class="sidebar-foot">
        <button type="button" class="sidebar-collapse-btn" @click="toggleCollapse()"
                :aria-pressed="collapsed ? 'true' : 'false'"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            <span class="nav-label">Collapse</span>
        </button>
    </div>
</aside>
