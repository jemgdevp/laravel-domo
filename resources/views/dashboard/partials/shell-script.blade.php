{{-- Core Alpine logic for the app shell: theme, sidebar/drawer, toasts, palette store. --}}
<script>
    (function () {
        const PALETTE_DEFAULT_ICON =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';

        document.addEventListener('alpine:init', () => {
            /* ---------------------------------------------------------------
               Alpine.store('palette') — registry pages push extra items into.
               item: { id, label, hint?, group?, icon?(html), href?, action?(fn) }
            --------------------------------------------------------------- */
            Alpine.store('palette', {
                items: [],
                register(items) {
                    const list = Array.isArray(items) ? items : [items];
                    list.forEach((it) => {
                        if (!it || !it.id) return;
                        const idx = this.items.findIndex((x) => x.id === it.id);
                        if (idx === -1) this.items.push(it);
                        else this.items.splice(idx, 1, it);
                    });
                },
                unregister(id) { this.items = this.items.filter((x) => x.id !== id); },
                clear() { this.items = []; },
            });

            // Event bridge so pages can register palette items without touching
            // the Alpine store directly:
            //   window.dispatchEvent(new CustomEvent('domo-palette:register', { detail: [{...}] }))
            window.addEventListener('domo-palette:register', (e) => {
                Alpine.store('palette').register(e.detail);
            });
            window.addEventListener('domo-palette:unregister', (e) => {
                Alpine.store('palette').unregister(e.detail);
            });

            /* ---------------------------------------------------------------
               domoShell — wraps <body>: theme + sidebar/drawer + toasts.
            --------------------------------------------------------------- */
            Alpine.data('domoShell', () => ({
                theme: document.documentElement.getAttribute('data-theme') || 'dark',
                collapsed: false,
                drawerOpen: false,
                isMobile: false,
                toasts: [],
                _seq: 0,
                _lastFocus: null,

                init() {
                    this.collapsed = localStorage.getItem('domo-sidebar-collapsed') === '1';
                    this.isMobile = window.matchMedia('(max-width: 900px)').matches;
                    window.matchMedia('(max-width: 900px)').addEventListener('change', (e) => {
                        this.isMobile = e.matches;
                        if (!e.matches) this.closeDrawer();
                    });
                    // React to OS theme changes only when user hasn't chosen explicitly.
                    window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (e) => {
                        if (!localStorage.getItem('domo-theme')) this.applyTheme(e.matches ? 'light' : 'dark');
                    });

                    // Global toast bridge: window.domoToast(message, type, timeout)
                    window.domoToast = (message, type = 'info', timeout) =>
                        this.pushToast({ message, type, timeout });
                },

                /* THEME */
                applyTheme(t) {
                    this.theme = t;
                    document.documentElement.setAttribute('data-theme', t);
                },
                toggleTheme() {
                    const next = this.theme === 'dark' ? 'light' : 'dark';
                    this.applyTheme(next);
                    localStorage.setItem('domo-theme', next);
                },

                /* SIDEBAR (desktop collapse) */
                toggleCollapse() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('domo-sidebar-collapsed', this.collapsed ? '1' : '0');
                },

                /* DRAWER (mobile) with focus trap */
                openDrawer() {
                    if (!this.isMobile) return;
                    this._lastFocus = document.activeElement;
                    this.drawerOpen = true;
                    document.body.style.overflow = 'hidden';
                    this.$nextTick(() => {
                        const first = this.$refs.sidebar?.querySelector('a, button');
                        first && first.focus();
                    });
                },
                closeDrawer() {
                    if (!this.drawerOpen) return;
                    this.drawerOpen = false;
                    document.body.style.overflow = '';
                    if (this._lastFocus && this._lastFocus.focus) this._lastFocus.focus();
                },
                trapDrawer(e) {
                    if (!this.drawerOpen || e.key !== 'Tab') return;
                    const focusables = this.$refs.sidebar.querySelectorAll(
                        'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );
                    if (!focusables.length) return;
                    const first = focusables[0];
                    const last = focusables[focusables.length - 1];
                    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
                },

                onEscape() { this.closeDrawer(); },

                openPalette() { window.dispatchEvent(new CustomEvent('domo-palette:open')); },

                /* TOASTS */
                pushToast(detail) {
                    const { message, type = 'info', timeout = 4500 } = detail || {};
                    if (!message) return;
                    const id = ++this._seq;
                    this.toasts.push({ id, message, type });
                    if (timeout > 0) setTimeout(() => this.dismissToast(id), timeout);
                    return id;
                },
                dismissToast(id) { this.toasts = this.toasts.filter((t) => t.id !== id); },
            }));

            /* ---------------------------------------------------------------
               domoPalette — the overlay component (uses Alpine.store('palette')).
            --------------------------------------------------------------- */
            Alpine.data('domoPalette', () => ({
                open: false,
                query: '',
                active: 0,
                defaultIcon: PALETTE_DEFAULT_ICON,

                // Built-in navigation + actions. Pages add more via the store.
                baseItems: [
                    { id: 'nav-dashboard', label: 'Dashboard', hint: 'g d', group: 'Navigate', href: @json(route('domo.index')),
                      icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>' },
                    { id: 'nav-schema', label: 'Schema', hint: 'g s', group: 'Navigate', href: @json(route('domo.schema')),
                      icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/></svg>' },
                    { id: 'nav-models', label: 'Models', hint: 'g m', group: 'Navigate', href: @json(route('domo.models')),
                      icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>' },
                    { id: 'nav-ai', label: 'AI Analysis', hint: 'g a', group: 'Navigate', href: @json(route('domo.analyze')),
                      icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/></svg>' },
                    { id: 'act-theme', label: 'Toggle theme', hint: 'appearance', group: 'Actions',
                      action: () => window.dispatchEvent(new CustomEvent('domo-toggle-theme')) },
                ],

                get allItems() {
                    return this.baseItems.concat(this.$store.palette.items);
                },
                get flat() {
                    const q = this.query.trim().toLowerCase();
                    const matched = !q
                        ? this.allItems
                        : this.allItems.filter((it) => {
                              const hay = (it.label + ' ' + (it.hint || '') + ' ' + (it.group || '')).toLowerCase();
                              return q.split(/\s+/).every((part) => hay.includes(part));
                          });
                    return matched.map((it, i) => ({ ...it, _i: i }));
                },
                get grouped() {
                    const groups = [];
                    this.flat.forEach((it) => {
                        const name = it.group || '';
                        let g = groups.find((x) => x.name === name);
                        if (!g) { g = { name, items: [] }; groups.push(g); }
                        g.items.push(it);
                    });
                    return groups;
                },

                show() {
                    this.open = true; this.query = ''; this.active = 0;
                    document.body.style.overflow = 'hidden';
                    this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
                },
                hide() {
                    this.open = false;
                    document.body.style.overflow = '';
                },
                toggle() { this.open ? this.hide() : this.show(); },
                trap(e) {
                    if (e.key !== 'Tab') return;
                    const focusables = this.$refs.panel.querySelectorAll(
                        'input, a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );
                    if (!focusables.length) return;
                    const first = focusables[0];
                    const last = focusables[focusables.length - 1];
                    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
                },
                move(dir) {
                    const n = this.flat.length;
                    if (!n) return;
                    this.active = (this.active + dir + n) % n;
                    this.$nextTick(() => {
                        const el = document.getElementById('palette-opt-' + this.active);
                        el && el.scrollIntoView({ block: 'nearest' });
                    });
                },
                choose() {
                    const item = this.flat[this.active];
                    if (item) this.select(item);
                },
                select(item) {
                    this.hide();
                    if (typeof item.action === 'function') { item.action(); return; }
                    if (item.href) { window.location.href = item.href; }
                },
            }));
        });
    })();
</script>
