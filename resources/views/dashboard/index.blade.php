@extends('domo::dashboard.layout')

@section('title', 'Dashboard')

@php
    // --- Defensive normalization (server-side data contract is FIXED) -----------
    // $tables: array of string|array|object. Reduce each to a plain table name.
    $tableNames = array_values(array_filter(array_map(function ($t) {
        if (is_array($t)) {
            return (string) reset($t);
        }
        if (is_object($t)) {
            $vars = get_object_vars($t);
            return $vars ? (string) reset($vars) : '';
        }
        return (string) $t;
    }, $tables ?? [])));

    $modelList = array_values(array_filter(array_map('strval', $models ?? [])));

    $tableCount = count($tableNames);
    $modelCount = count($modelList);

    // --- Environment / config (derived only from allowed config keys) -----------
    $dbConnection = (string) config('database.default');
    $dbDriver = (string) config('database.connections.' . $dbConnection . '.driver');
    $aiDriver = (string) config('domo.ai_driver');
    $aiConfigured = $aiDriver !== '';
    $appVersion = (string) config('app.version', '0.1.0');
    $dashboardRoute = (string) config('domo.dashboard.route', 'domo');
@endphp

@section('content')
<div
    class="animate-fade-in"
    x-data="domoHome({
        tables: @js($tableNames),
        models: @js($modelList),
        schemaUrl: @js(route('domo.schema')),
        modelsUrl: @js(route('domo.models')),
    })"
>
    {{-- ============================================================ HERO --}}
    <div class="page-header">
        <div class="page-eyebrow">
            <span class="pixel-dot" aria-hidden="true" style="display:inline-block;vertical-align:middle;margin-right:8px;"></span>
            Workspace · Overview
        </div>
        <h1 class="page-title">Welcome to Laravel&nbsp;Domo</h1>
        <p class="page-subtitle">
            Your AI-powered database orchestrator — inspect tables, browse Eloquent models, and run schema analysis.
        </p>
    </div>

    {{-- ============================================================ STATS --}}
    <div class="stats-grid">
        {{-- Total Tables --}}
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>
                Total Tables
            </div>
            <div class="stat-value">{{ $tableCount }}</div>
            <div class="stat-delta {{ $tableCount > 0 ? 'is-up' : 'is-flat' }}">
                {{ $tableCount > 0 ? 'Database ready' : 'No tables detected' }}
            </div>
        </div>

        {{-- Eloquent Models --}}
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                Eloquent Models
            </div>
            <div class="stat-value">{{ $modelCount }}</div>
            <div class="stat-delta {{ $modelCount > 0 ? 'is-up' : 'is-flat' }}">
                {{ $modelCount > 0 ? 'Detected in app' : 'None found' }}
            </div>
        </div>

        {{-- Database + driver --}}
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="2" y="4" width="20" height="6" rx="1"/><rect x="2" y="14" width="20" height="6" rx="1"/><line x1="6" y1="7" x2="6.01" y2="7"/><line x1="6" y1="17" x2="6.01" y2="17"/></svg>
                Database
            </div>
            <div class="stat-value is-sm truncate" title="{{ $dbConnection }}">{{ $dbConnection ?: '—' }}</div>
            <div class="stat-delta is-flat">{{ $dbDriver ?: 'unknown driver' }}</div>
        </div>

        {{-- AI driver / status --}}
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M12 8V4M8 2h8"/><circle cx="9" cy="14" r="1"/><circle cx="15" cy="14" r="1"/></svg>
                AI Driver
            </div>
            <div class="stat-value is-sm truncate" title="{{ $aiDriver }}">{{ $aiConfigured ? $aiDriver : 'none' }}</div>
            <div class="stat-delta {{ $aiConfigured ? 'is-up' : 'is-flat' }}">
                {{ $aiConfigured ? 'Configured' : 'Not configured' }}
            </div>
        </div>
    </div>

    {{-- ============================================================ QUICK ACTIONS --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Quick Actions
            </h2>
            <div class="card-actions">
                <button class="btn btn-ghost btn-sm" @click="$dispatch('domo-palette:open')">
                    <kbd class="kbd">⌘K</kbd>
                    <span>Search</span>
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('domo.schema') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/></svg>
                View Schema
            </a>
            <a href="{{ route('domo.models') }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>
                View Models
            </a>
            <a href="{{ route('domo.analyze') }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/></svg>
                AI Analysis
            </a>
        </div>
    </div>

    {{-- ============================================================ ENVIRONMENT / STATUS --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Environment
            </h2>
            <span class="badge badge-success has-pulse"><span class="dot"></span> Live</span>
        </div>

        <div class="grid-auto">
            {{-- DB connection --}}
            <div class="flex items-center justify-between gap-3">
                <span class="text-secondary text-sm">DB connection</span>
                <code class="mono">{{ $dbConnection ?: '—' }}</code>
            </div>
            {{-- Driver --}}
            <div class="flex items-center justify-between gap-3">
                <span class="text-secondary text-sm">Driver</span>
                <code class="mono">{{ $dbDriver ?: 'unknown' }}</code>
            </div>
            {{-- AI provider --}}
            <div class="flex items-center justify-between gap-3">
                <span class="text-secondary text-sm">AI provider</span>
                @if($aiConfigured)
                    <span class="badge badge-primary"><span class="dot"></span> {{ $aiDriver }}</span>
                @else
                    <span class="badge badge-muted"><span class="dot"></span> not set</span>
                @endif
            </div>
            {{-- Package version --}}
            <div class="flex items-center justify-between gap-3">
                <span class="text-secondary text-sm">Package version</span>
                <code class="mono">v{{ $appVersion }}</code>
            </div>
            {{-- Dashboard route --}}
            <div class="flex items-center justify-between gap-3">
                <span class="text-secondary text-sm">Dashboard route</span>
                <code class="mono">/{{ $dashboardRoute }}</code>
            </div>
            {{-- Detected tables/models summary --}}
            <div class="flex items-center justify-between gap-3">
                <span class="text-secondary text-sm">Detected</span>
                <span class="mono text-secondary text-sm">
                    <span class="text-accent">{{ $tableCount }}</span> tables ·
                    <span class="text-accent">{{ $modelCount }}</span> models
                </span>
            </div>
        </div>
    </div>

    {{-- ============================================================ TABLES OVERVIEW --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="12" y1="3" x2="12" y2="21"/></svg>
                Database Tables
                <span class="badge badge-muted mono" x-show="tables.length" role="status" aria-live="polite">
                    <span x-text="filteredTables.length"></span> / <span x-text="tables.length"></span>
                    <span class="sr-only"> tables shown</span>
                </span>
            </h2>
            <div class="card-actions">
                <a href="{{ route('domo.schema') }}" class="btn btn-secondary btn-sm">View all</a>
            </div>
        </div>

        @if($tableCount > 0)
            {{-- Search --}}
            <div class="search mb-4" style="max-width:340px;">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                    class="input is-mono"
                    type="text"
                    x-model="tableQuery"
                    placeholder="Filter tables…"
                    aria-label="Filter tables"
                >
                <button class="search-clear" x-show="tableQuery" x-cloak @click="tableQuery=''" aria-label="Clear table filter">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="table-wrap" style="--table-max-h: 420px;">
                <table class="table is-zebra">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th>Status</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="name in filteredTables" :key="name">
                            <tr>
                                <td class="cell-mono"><span x-text="name"></span></td>
                                <td>
                                    <span class="badge badge-success"><span class="dot"></span> Active</span>
                                </td>
                                <td style="text-align:right;">
                                    <a class="btn btn-ghost btn-sm" :href="schemaUrl + '#' + encodeURIComponent(name)">
                                        View schema
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- No-match (client-side) empty state --}}
            <div class="empty-state" x-show="!filteredTables.length" x-cloak role="status" aria-live="polite">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div class="empty-title">No tables match</div>
                <p class="empty-hint">No table name contains “<span class="mono" x-text="tableQuery"></span>”. Try a different filter.</p>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/></svg>
                </div>
                <div class="empty-title">No tables found</div>
                <p class="empty-hint">No tables were detected on the <code class="mono">{{ $dbConnection }}</code> connection. Run your migrations to populate the database.</p>
            </div>
        @endif
    </div>

    {{-- ============================================================ MODELS OVERVIEW --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>
                Eloquent Models
                <span class="badge badge-muted mono" x-show="models.length" role="status" aria-live="polite">
                    <span x-text="filteredModels.length"></span> / <span x-text="models.length"></span>
                    <span class="sr-only"> models shown</span>
                </span>
            </h2>
            <div class="card-actions">
                <a href="{{ route('domo.models') }}" class="btn btn-secondary btn-sm">View all</a>
            </div>
        </div>

        @if($modelCount > 0)
            {{-- Search --}}
            <div class="search mb-4" style="max-width:340px;">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                    class="input is-mono"
                    type="text"
                    x-model="modelQuery"
                    placeholder="Filter models…"
                    aria-label="Filter models"
                >
                <button class="search-clear" x-show="modelQuery" x-cloak @click="modelQuery=''" aria-label="Clear model filter">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="models-overview" x-show="filteredModels.length">
              <div class="grid-auto">
                <template x-for="fqcn in filteredModels" :key="fqcn">
                    <div class="card model-card" style="margin-bottom: 0;">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="font-semibold truncate" x-text="basename(fqcn)" :title="fqcn"></span>
                            <button
                                class="btn-icon btn-sm is-bordered"
                                @click="copyFqcn(fqcn)"
                                :aria-label="'Copy ' + fqcn"
                                title="Copy fully-qualified class name"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </button>
                        </div>
                        <code class="mono text-xs truncate" style="display:block;" x-text="fqcn" :title="fqcn"></code>
                    </div>
                </template>
              </div>
            </div>

            {{-- No-match (client-side) empty state --}}
            <div class="empty-state" x-show="!filteredModels.length" x-cloak role="status" aria-live="polite">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div class="empty-title">No models match</div>
                <p class="empty-hint">No model contains “<span class="mono" x-text="modelQuery"></span>”. Try a different filter.</p>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>
                </div>
                <div class="empty-title">No models found</div>
                <p class="empty-hint">Add Eloquent models under <code class="mono">app/Models</code> to see them here.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Visually-hidden helper for screen-reader-only status text.
       No global .sr-only utility exists in the design system, so it is
       page-scoped here (mirrors models.blade.php / analyze.blade.php). */
    .sr-only {
        position: absolute; width: 1px; height: 1px;
        padding: 0; margin: -1px; overflow: hidden;
        clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
    }

    /* Model card hover lift, composed on top of the shared .card surface so the
       Dashboard overview matches the Models page (same surface + radius). */
    .model-card {
        transition: border-color var(--t-fast), transform var(--t-fast);
    }
    .model-card:hover { border-color: var(--border-strong); transform: translateY(-2px); }

    /* Cap the models overview height for scroll parity with the tables block
       (which uses --table-max-h: 420px). Keeps both overview sections at the
       same density/scroll behavior on long databases. */
    .models-overview {
        max-height: 420px;
        overflow: auto;
    }

    /* Respect users who prefer reduced motion: no hover translate. */
    @media (prefers-reduced-motion: reduce) {
        .model-card,
        .model-card:hover { transition: none; transform: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('domoHome', (cfg) => ({
            tables: cfg.tables || [],
            models: cfg.models || [],
            schemaUrl: cfg.schemaUrl,
            modelsUrl: cfg.modelsUrl,
            tableQuery: '',
            modelQuery: '',

            init() {
                // Register tables + models into the command palette (idempotent by id).
                const items = [];
                this.tables.forEach((t) => items.push({
                    id: 'tbl-' + t,
                    label: t,
                    hint: 'table',
                    group: 'Tables',
                    href: this.schemaUrl + '#' + encodeURIComponent(t),
                }));
                this.models.forEach((m) => items.push({
                    id: 'mdl-' + m,
                    label: this.basename(m),
                    hint: m,
                    group: 'Models',
                    href: this.modelsUrl,
                }));
                if (items.length) {
                    window.dispatchEvent(new CustomEvent('domo-palette:register', { detail: items }));
                }
            },

            basename(fqcn) {
                const s = String(fqcn);
                const parts = s.split('\\');
                return parts[parts.length - 1] || s;
            },

            get filteredTables() {
                const q = this.tableQuery.trim().toLowerCase();
                if (!q) return this.tables;
                return this.tables.filter((t) => t.toLowerCase().includes(q));
            },

            get filteredModels() {
                const q = this.modelQuery.trim().toLowerCase();
                if (!q) return this.models;
                return this.models.filter((m) => m.toLowerCase().includes(q));
            },

            async copyFqcn(fqcn) {
                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(fqcn);
                    } else {
                        const ta = document.createElement('textarea');
                        ta.value = fqcn;
                        ta.style.position = 'fixed';
                        ta.style.opacity = '0';
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                    }
                    window.domoToast('Copied ' + this.basename(fqcn) + ' FQCN', 'success');
                } catch (e) {
                    window.domoToast('Copy failed — clipboard unavailable', 'error');
                }
            },
        }));
    });
</script>
@endpush
