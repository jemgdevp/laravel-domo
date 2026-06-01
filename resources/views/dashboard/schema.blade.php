@extends('domo::dashboard.layout')

@section('title', 'Schema')

@php
    /*
     | Normalize the FIXED server-side contract into a plain, JSON-safe array we
     | can both render with Blade AND hand to Alpine for client-side filtering.
     | $schemas = [ tableName => ['columns' => [ col, ... ], ... ] ]
     | each col is object OR array with Field/Type/Null/Key/Default.
     | Defensive reads only — no new controller data is required.
     */
    $read = static function ($col, string $key, $fallback = null) {
        if (is_object($col)) {
            return $col->{$key} ?? $fallback;
        }
        if (is_array($col)) {
            return $col[$key] ?? $fallback;
        }
        return $fallback;
    };

    $tablesData = [];
    $totalColumns = 0;
    $totalKeys = 0;

    foreach ($schemas as $tableName => $schema) {
        $columns = [];
        $rawColumns = (is_array($schema) && isset($schema['columns'])) ? $schema['columns'] : [];

        if (is_iterable($rawColumns)) {
            foreach ($rawColumns as $col) {
                $field = (string) ($read($col, 'Field', '') ?? '');
                $type = (string) ($read($col, 'Type', '') ?? '');
                $null = (string) ($read($col, 'Null', '') ?? '');
                $key = strtoupper((string) ($read($col, 'Key', '') ?? ''));
                $defaultRaw = $read($col, 'Default', null);
                $hasDefault = ! ($defaultRaw === null || $defaultRaw === '');

                $columns[] = [
                    'field' => $field,
                    'type' => $type,
                    'null' => strtoupper($null) === 'YES',
                    'nullRaw' => $null,
                    'key' => $key,
                    'default' => $hasDefault ? (string) $defaultRaw : null,
                ];

                if (in_array($key, ['PRI', 'UNI', 'MUL'], true)) {
                    $totalKeys++;
                }
            }
        }

        $totalColumns += count($columns);

        $tablesData[] = [
            'name' => (string) $tableName,
            'columns' => $columns,
            'count' => count($columns),
            'hasKey' => collect($columns)->contains(fn ($c) => $c['key'] === 'PRI'),
        ];
    }

    $tableCount = count($tablesData);
@endphp

@section('content')
<div
    x-data="domoSchemaExplorer({{ Js::from($tablesData) }})"
    x-init="init()"
    x-effect="applyFilter()"
>
    <div class="page-header">
        <div class="page-eyebrow">Workspace</div>
        <h1 class="page-title">Database Schema</h1>
        <p class="page-subtitle">Inspect every table, column, key, and default across your connection.</p>
    </div>

    {{-- KPI summary --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>
                Total Tables
            </div>
            <div class="stat-value">{{ $tableCount }}</div>
            <div class="stat-delta {{ $tableCount > 0 ? 'is-up' : 'is-flat' }}">
                {{ $tableCount > 0 ? 'Schema loaded' : 'No tables' }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                Total Columns
            </div>
            <div class="stat-value">{{ $totalColumns }}</div>
            <div class="stat-delta is-flat">Across all tables</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                Key Columns
            </div>
            <div class="stat-value">{{ $totalKeys }}</div>
            <div class="stat-delta is-flat">PRI · UNI · MUL</div>
        </div>
    </div>

    @if ($tableCount === 0)
        {{-- Real empty state: no tables at all --}}
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/></svg>
                </div>
                <div class="empty-title">No tables found</div>
                <p class="empty-hint">This connection reports no inspectable tables. Run your migrations, then reload to explore the schema.</p>
            </div>
        </div>
    @else
        {{-- Toolbar: global search + expand/collapse-all + live result count --}}
        <div class="card">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="search flex-1" style="min-width: 240px; max-width: 460px;">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input
                        type="text"
                        class="input is-mono"
                        x-model="query"
                        @keydown.escape.stop="query = ''"
                        placeholder="Filter tables &amp; columns…"
                        aria-label="Filter tables and columns"
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <button
                        class="search-clear"
                        x-show="query"
                        x-cloak
                        @click="query = ''"
                        aria-label="Clear filter"
                        type="button"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-secondary text-sm mono" aria-live="polite">
                        <span x-text="visibleCount"></span><span x-show="query" x-cloak> / {{ $tableCount }}</span>
                        <span x-text="visibleCount === 1 ? 'table' : 'tables'"></span>
                    </span>
                    <button class="btn btn-secondary btn-sm" @click="expandAll()" type="button" :disabled="!visibleCount">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/></svg>
                        Expand all
                    </button>
                    <button class="btn btn-ghost btn-sm" @click="collapseAll()" type="button" :disabled="!visibleCount">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 11 12 6 7 11"/><polyline points="17 18 12 13 7 18"/></svg>
                        Collapse all
                    </button>
                </div>
            </div>
        </div>

        {{-- No-match empty state (filter active, nothing matches) --}}
        <div class="card" x-show="visibleCount === 0" x-cloak>
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                </div>
                <div class="empty-title">No matches</div>
                <p class="empty-hint">
                    No table or column matches <code x-text="query"></code>.
                    <button class="btn btn-ghost btn-sm mt-3" @click="query = ''" type="button">Clear filter</button>
                </p>
            </div>
        </div>

        {{-- Table cards --}}
        <template x-for="table in tables" :key="table.name">
            <div
                class="card is-flush schema-table"
                x-show="table._visible"
                x-cloak
                :id="'table-' + table.name"
                tabindex="-1"
            >
                {{-- Collapsible header (button row) --}}
                <h2 class="schema-table-head">
                    <button
                        class="schema-toggle"
                        type="button"
                        @click="table._open = !table._open"
                        :aria-expanded="table._open ? 'true' : 'false'"
                        :aria-controls="'panel-' + table.name"
                    >
                        <svg class="schema-chevron" :class="{ 'is-open': table._open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                        <svg class="schema-table-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>
                        <span class="schema-table-name mono" x-text="table.name"></span>
                        <span class="badge badge-muted" x-show="table.columns.length">
                            <span x-text="table._matchCount + ' / ' + table.columns.length"></span>
                            <span x-text="table.columns.length === 1 ? 'col' : 'cols'"></span>
                        </span>
                        <span class="badge badge-primary" x-show="table.hasKey" x-cloak title="Has a primary key">
                            <span class="dot"></span> PK
                        </span>
                    </button>

                    <span class="card-actions">
                        <button
                            class="btn-icon btn-sm is-bordered"
                            type="button"
                            @click.stop="copy(table.name, 'Table name')"
                            :aria-label="'Copy table name ' + table.name"
                            title="Copy table name"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        </button>
                    </span>
                </h2>

                {{-- Collapsible body --}}
                <div
                    class="schema-panel"
                    :id="'panel-' + table.name"
                    x-show="table._open"
                    x-collapse
                    role="region"
                    :aria-label="'Columns of ' + table.name"
                >
                    {{-- Table has no columns: per-table empty state --}}
                    <template x-if="!table.columns.length">
                        <div class="empty-state" style="padding: var(--space-6) var(--space-5);">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                            </div>
                            <div class="empty-title">No columns reported</div>
                            <p class="empty-hint">The analyzer returned no column metadata for this table.</p>
                        </div>
                    </template>

                    <template x-if="table.columns.length">
                        <div class="table-wrap" style="--table-max-h: 460px; border: none; border-radius: 0; border-top: 1px solid var(--border-subtle);">
                            <table class="table is-zebra">
                                <thead>
                                    <tr>
                                        <th style="width: 36%;">Column</th>
                                        <th>Type</th>
                                        <th>Null</th>
                                        <th>Key</th>
                                        <th>Default</th>
                                        <th aria-label="Actions"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="col in table.columns" :key="table.name + '.' + col.field">
                                        <tr x-show="col._match" :class="{ 'is-key': col.key === 'PRI' }">
                                            <td class="cell-mono">
                                                <span class="schema-col-name" :class="{ 'is-key': col.key === 'PRI' }">
                                                    <svg x-show="col.key === 'PRI'" x-cloak class="schema-key-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                                                    <span x-text="col.field"></span>
                                                </span>
                                            </td>
                                            <td>
                                                <code x-text="col.type || '—'"></code>
                                            </td>
                                            <td>
                                                <template x-if="col.null">
                                                    <span class="badge badge-muted">NULL</span>
                                                </template>
                                                <template x-if="!col.null">
                                                    <span class="badge badge-warning">NOT&nbsp;NULL</span>
                                                </template>
                                            </td>
                                            <td>
                                                <template x-if="col.key === 'PRI'">
                                                    <span class="badge badge-primary"><span class="dot"></span> PRI</span>
                                                </template>
                                                <template x-if="col.key === 'UNI'">
                                                    <span class="badge badge-info"><span class="dot"></span> UNI</span>
                                                </template>
                                                <template x-if="col.key === 'MUL'">
                                                    <span class="badge badge-success"><span class="dot"></span> MUL</span>
                                                </template>
                                                <template x-if="!['PRI','UNI','MUL'].includes(col.key)">
                                                    <span class="cell-muted">—</span>
                                                </template>
                                            </td>
                                            <td class="cell-mono">
                                                <template x-if="col.default !== null">
                                                    <code x-text="col.default"></code>
                                                </template>
                                                <template x-if="col.default === null">
                                                    <span class="cell-muted">NULL</span>
                                                </template>
                                            </td>
                                            <td style="text-align: right;">
                                                <button
                                                    class="btn-icon btn-sm"
                                                    type="button"
                                                    @click="copy(col.field, 'Column name')"
                                                    :aria-label="'Copy column name ' + col.field"
                                                    title="Copy column name"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Schema explorer — composed on top of the locked design system.
       Layout/structure only; visual tokens come from design-system variables. */
    .schema-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--space-3);
        padding: var(--space-3) var(--space-4) var(--space-3) var(--space-2);
    }
    .schema-toggle {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        flex: 1;
        min-width: 0;
        padding: var(--space-2) var(--space-2);
        background: transparent;
        border: none;
        border-radius: var(--radius-md);
        color: var(--text-primary);
        cursor: pointer;
        text-align: left;
        font: inherit;
        transition: background var(--t-fast);
    }
    .schema-toggle:hover { background: var(--hover-overlay); }
    .schema-toggle:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
    .schema-chevron {
        width: 16px; height: 16px; flex-shrink: 0;
        stroke: var(--text-muted);
        transition: transform var(--t-base), stroke var(--t-fast);
    }
    .schema-chevron.is-open { transform: rotate(90deg); stroke: var(--accent); }
    .schema-table-icon { width: 17px; height: 17px; flex-shrink: 0; stroke: var(--accent); }
    .schema-table-name {
        font-size: 0.95rem; font-weight: 700; letter-spacing: -0.01em;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .schema-toggle:hover .schema-table-name { color: var(--text-primary); }

    /* Key/primary column emphasis inside the table */
    .table tbody tr.is-key { background: var(--accent-soft); }
    .table.is-zebra tbody tr.is-key:nth-child(even) { background: var(--accent-soft); }
    .table tbody tr.is-key:hover,
    .table.is-zebra tbody tr.is-key:nth-child(even):hover { background: var(--accent-soft-hover); }

    .schema-col-name { display: inline-flex; align-items: center; gap: var(--space-2); }
    .schema-col-name.is-key { color: var(--accent); font-weight: 700; }
    .schema-key-icon { width: 13px; height: 13px; flex-shrink: 0; stroke: var(--accent); }

    /* Flash a table when jumped-to from the command palette */
    .schema-table.is-jumped { animation: schema-jump-flash 1.6s var(--t-base) both; }
    @keyframes schema-jump-flash {
        0%, 100% { box-shadow: 0 0 0 0 transparent; border-color: var(--border-color); }
        18% { box-shadow: 0 0 0 3px var(--accent-soft), var(--shadow-glow); border-color: var(--accent); }
    }
    @media (prefers-reduced-motion: reduce) {
        .schema-table.is-jumped { animation: none; }
        .schema-chevron { transition: none; }
    }
</style>
@endpush

@push('scripts')
{{-- x-collapse plugin is loaded in the layout <head> before Alpine core (correct order). --}}
<script>
    function domoSchemaExplorer(initialTables) {
        return {
            query: '',
            tables: (initialTables || []).map((t) => ({
                ...t,
                _open: false,
                _visible: true,
                _matchCount: t.columns.length,
                columns: t.columns.map((c) => ({ ...c, _match: true })),
            })),

            init() {
                // Open the first few tables by default for an inviting first view.
                this.tables.slice(0, 3).forEach((t) => { t._open = true; });
                this.registerPalette();
                this.handleDeepLink();
            },

            get visibleCount() {
                return this.tables.reduce((n, t) => n + (t._visible ? 1 : 0), 0);
            },

            applyFilter() {
                const q = this.query.trim().toLowerCase();
                const tokens = q ? q.split(/\s+/) : [];

                this.tables.forEach((t) => {
                    const tableHit = tokens.length === 0 || tokens.every((tk) => t.name.toLowerCase().includes(tk));

                    let matchCount = 0;
                    t.columns.forEach((c) => {
                        let m;
                        if (tokens.length === 0) {
                            m = true;
                        } else if (tableHit) {
                            // Whole table matched by name → show all its columns.
                            m = true;
                        } else {
                            const hay = (c.field + ' ' + c.type + ' ' + c.key).toLowerCase();
                            m = tokens.every((tk) => hay.includes(tk));
                        }
                        c._match = m;
                        if (m) matchCount++;
                    });

                    t._matchCount = matchCount;
                    // A table is visible if its name matches OR any column matched.
                    t._visible = tableHit || matchCount > 0;

                    // Auto-expand tables that surfaced because of a column match.
                    if (q && t._visible && matchCount > 0 && !tableHit) {
                        t._open = true;
                    }
                });
            },

            expandAll() {
                this.tables.forEach((t) => { if (t._visible) t._open = true; });
            },
            collapseAll() {
                this.tables.forEach((t) => { t._open = false; });
            },

            async copy(text, label) {
                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        await navigator.clipboard.writeText(text);
                    } else {
                        const ta = document.createElement('textarea');
                        ta.value = text;
                        ta.style.position = 'fixed';
                        ta.style.opacity = '0';
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                    }
                    window.domoToast(`${label} copied: ${text}`, 'success');
                } catch (e) {
                    window.domoToast(`Could not copy ${label.toLowerCase()}`, 'error');
                }
            },

            jumpTo(name) {
                const target = this.tables.find((t) => t.name === name);
                if (!target) return;
                this.query = '';
                this.applyFilter();
                target._open = true;
                this.$nextTick(() => {
                    const el = document.getElementById('table-' + name);
                    if (!el) return;
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    el.classList.remove('is-jumped');
                    void el.offsetWidth; // reflow to restart animation
                    el.classList.add('is-jumped');
                    el.focus({ preventScroll: true });
                });
            },

            handleDeepLink() {
                const hash = decodeURIComponent(window.location.hash.replace(/^#/, '')).replace(/^table-/, '');
                if (hash) this.$nextTick(() => this.jumpTo(hash));
                window.addEventListener('hashchange', () => {
                    const h = decodeURIComponent(window.location.hash.replace(/^#/, '')).replace(/^table-/, '');
                    if (h) this.jumpTo(h);
                });
            },

            registerPalette() {
                const tableIcon =
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/></svg>';
                const items = this.tables.map((t) => ({
                    id: 'schema-tbl-' + t.name,
                    label: t.name,
                    hint: t.columns.length + (t.columns.length === 1 ? ' column' : ' columns'),
                    group: 'Tables',
                    icon: tableIcon,
                    action: () => this.jumpTo(t.name),
                }));
                if (items.length) {
                    window.dispatchEvent(new CustomEvent('domo-palette:register', { detail: items }));
                }
            },
        };
    }
</script>
@endpush
