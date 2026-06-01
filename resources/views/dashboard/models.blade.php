@extends('domo::dashboard.layout')

@section('title', 'Models')

@php
    // Shape the locked controller data into a flat, view-friendly array.
    // $models = list of FQCN strings; $relationships = [fqcn => [method => relatedClass]].
    $modelCards = [];
    foreach ($models as $model) {
        $rels = [];
        if (isset($relationships[$model]) && is_array($relationships[$model])) {
            foreach ($relationships[$model] as $method => $related) {
                $rels[] = [
                    'method' => $method,
                    'related' => $related,
                    'relatedBase' => class_basename($related),
                ];
            }
        }
        $modelCards[] = [
            'fqcn' => $model,
            'base' => class_basename($model),
            'relationships' => $rels,
        ];
    }
    $totalRelationships = array_sum(array_map(fn ($c) => count($c['relationships']), $modelCards));
    $modelsWithRels = count(array_filter($modelCards, fn ($c) => count($c['relationships']) > 0));
@endphp

@section('content')
<div
    x-data="domoModels({{ Js::from($modelCards) }})"
    x-init="init()"
>
    {{-- Page heading --}}
    <div class="page-header">
        <div class="page-eyebrow">Workspace</div>
        <h1 class="page-title">Eloquent Models</h1>
        <p class="page-subtitle">Browse discovered models, inspect their relationships, and ask the AI to map associations.</p>
    </div>

    {{-- KPI strip --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                Total Models
            </div>
            <div class="stat-value">{{ count($modelCards) }}</div>
            <div class="stat-delta {{ count($modelCards) > 0 ? 'is-up' : 'is-flat' }}">
                {{ count($modelCards) > 0 ? 'Discovered in app/Models' : 'None found yet' }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 0 1 0 10h-2"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Relationships
            </div>
            <div class="stat-value">{{ $totalRelationships }}</div>
            <div class="stat-delta {{ $totalRelationships > 0 ? 'is-up' : 'is-flat' }}">
                {{ $modelsWithRels }} of {{ count($modelCards) }} models linked
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/></svg>
                Showing
            </div>
            <div class="stat-value mono" x-text="filtered.length"></div>
            <div class="stat-delta is-flat">
                <span x-show="!query.trim()" x-cloak>All models</span>
                <span x-show="query.trim()" x-cloak x-text="`Matching “${query.trim()}”`"></span>
            </div>
        </div>
    </div>

    @if(count($modelCards) === 0)
        {{-- No models discovered at all --}}
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>
                </div>
                <div class="empty-title">No models found</div>
                <p class="empty-hint">Add Eloquent models to <code>app/Models</code> to see them listed here with their relationships.</p>
            </div>
        </div>
    @else
        {{-- Toolbar: search --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>
                    Model Explorer
                </h2>
                <div class="card-actions">
                    <span class="badge badge-muted mono" x-text="`${filtered.length} / {{ count($modelCards) }}`"></span>
                </div>
            </div>

            <div class="search" role="search">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <label for="model-search" class="sr-only">Filter models</label>
                <input
                    id="model-search"
                    class="input is-mono"
                    type="text"
                    autocomplete="off"
                    spellcheck="false"
                    placeholder="Filter by class name or namespace…"
                    x-model="query"
                    @keydown.escape.stop="query = ''"
                >
                <button class="search-clear" type="button" x-show="query" x-cloak @click="query = ''; $refs && document.getElementById('model-search').focus()" aria-label="Clear search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        {{-- Model grid --}}
        <div class="grid-auto" x-show="filtered.length > 0">
            <template x-for="model in filtered" :key="model.fqcn">
                <div class="card model-card animate-fade-in" style="margin-bottom: 0; display: flex; flex-direction: column;">
                    <div class="card-header" style="margin-bottom: var(--space-4);">
                        <h3 class="card-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>
                            <span x-text="model.base"></span>
                        </h3>
                        <div class="card-actions">
                            <span
                                class="badge"
                                :class="model.relationships.length ? 'badge-primary' : 'badge-muted'"
                                x-text="model.relationships.length + (model.relationships.length === 1 ? ' rel' : ' rels')"
                            ></span>
                        </div>
                    </div>

                    {{-- FQCN + copy --}}
                    <div class="flex items-center gap-2 mb-4" style="min-width: 0;">
                        <code class="truncate" style="flex: 1; min-width: 0;" x-text="model.fqcn" :title="model.fqcn"></code>
                        <button
                            class="btn-icon btn-sm is-bordered"
                            type="button"
                            @click="copyFqcn(model.fqcn)"
                            :aria-label="'Copy ' + model.fqcn"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        </button>
                    </div>

                    {{-- Relationships --}}
                    <div style="flex: 1;">
                        <template x-if="model.relationships.length > 0">
                            <div>
                                <div class="text-xs text-muted mono mb-2" style="text-transform: uppercase; letter-spacing: 0.08em;">Relationships</div>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="rel in model.relationships" :key="rel.method">
                                        <span class="badge badge-info" :title="rel.method + ' → ' + rel.related">
                                            <span class="dot"></span>
                                            <span x-text="rel.method + ' → ' + rel.relatedBase"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="model.relationships.length === 0">
                            <p class="text-sm text-muted" style="display: flex; align-items: center; gap: var(--space-2);">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                No relationships detected
                            </p>
                        </template>
                    </div>

                    {{-- AI analysis trigger --}}
                    <hr class="divider" style="margin: var(--space-4) 0;">
                    <div class="flex items-center justify-between gap-3">
                        <button
                            class="btn btn-primary btn-sm"
                            type="button"
                            @click="analyze(model)"
                            :disabled="isLoading(model.fqcn)"
                            :aria-label="'Analyze ' + model.base + ' with AI'"
                        >
                            <span class="spinner" x-show="isLoading(model.fqcn)" x-cloak></span>
                            <svg x-show="!isLoading(model.fqcn)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l1.9 5.8L20 10.5l-5.8 1.9L12 18l-1.9-5.6L4 10.5l6.1-1.7z"/></svg>
                            <span x-text="isLoading(model.fqcn) ? 'Analyzing…' : 'Analyze with AI'"></span>
                        </button>
                        <button
                            class="btn btn-ghost btn-sm"
                            type="button"
                            x-show="hasResult(model.fqcn)"
                            x-cloak
                            @click="toggleResult(model.fqcn)"
                            :aria-expanded="isOpen(model.fqcn).toString()"
                        >
                            <span x-text="isOpen(model.fqcn) ? 'Hide result' : 'Show result'"></span>
                        </button>
                    </div>

                    {{-- Loading skeleton (decorative) + a polite live region that
                         announces analysis progress/readiness to screen readers,
                         since the skeleton itself is aria-hidden. --}}
                    <div x-show="isLoading(model.fqcn)" x-cloak class="mt-4" aria-hidden="true">
                        <div class="skeleton is-text" style="width: 90%"></div>
                        <div class="skeleton is-text" style="width: 75%"></div>
                        <div class="skeleton is-text" style="width: 82%"></div>
                    </div>
                    <p
                        class="sr-only"
                        role="status"
                        aria-live="polite"
                        x-text="isLoading(model.fqcn)
                            ? ('Analyzing ' + model.base + ' with AI…')
                            : (hasResult(model.fqcn) ? ('AI analysis ready for ' + model.base + '.') : '')"
                    ></p>

                    {{-- AI result — wrapped in a polite live region so the
                         'AI result' state + content are announced when the
                         panel toggles open (SC 4.1.3), matching the Analyze page. --}}
                    <div
                        x-show="hasResult(model.fqcn) && isOpen(model.fqcn)"
                        x-cloak
                        class="mt-4 animate-fade-in"
                        role="status"
                        aria-live="polite"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="badge badge-success"><span class="dot"></span> AI result</span>
                            <button
                                class="btn-icon btn-sm is-bordered"
                                type="button"
                                @click="copyResult(model.fqcn)"
                                aria-label="Copy AI result"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </button>
                        </div>
                        <pre class="code-block"><code x-text="resultText(model.fqcn)"></code></pre>
                    </div>
                </div>
            </template>
        </div>

        {{-- Filtered-to-empty state --}}
        <div class="card" x-show="filtered.length === 0" x-cloak>
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div class="empty-title">No matching models</div>
                <p class="empty-hint">
                    Nothing matches <code x-text="query.trim()"></code>.
                </p>
                <button class="btn btn-secondary btn-sm mt-2" type="button" @click="query = ''">Clear filter</button>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Visually-hidden label helper (keeps search input labelled for SR users). */
    .sr-only {
        position: absolute; width: 1px; height: 1px;
        padding: 0; margin: -1px; overflow: hidden;
        clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
    }
    /* Subtle lift on model cards (uses design-system tokens only). */
    .model-card { transition: transform var(--t-base), border-color var(--t-base); }
    .model-card:hover { transform: translateY(-2px); border-color: var(--border-strong); }
    @media (prefers-reduced-motion: reduce) {
        .model-card:hover { transform: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    function domoModels(models) {
        return {
            models: models,
            query: '',
            // Per-model state keyed by FQCN.
            loading: {},   // fqcn -> bool
            results: {},   // fqcn -> formatted string
            openResult: {},// fqcn -> bool (result panel expanded)

            init() {
                // Register each model in the ⌘K command palette (idempotent by id).
                if (this.models.length) {
                    window.dispatchEvent(new CustomEvent('domo-palette:register', {
                        detail: this.models.map((m) => ({
                            id: 'model-' + m.fqcn,
                            label: m.base,
                            hint: m.fqcn,
                            group: 'Models',
                            action: () => {
                                this.query = m.base;
                                this.copyFqcn(m.fqcn);
                            },
                        })),
                    }));
                }
            },

            get filtered() {
                const q = this.query.trim().toLowerCase();
                if (!q) return this.models;
                return this.models.filter((m) =>
                    (m.base + ' ' + m.fqcn).toLowerCase().includes(q)
                );
            },

            /* ---- per-model helpers ---- */
            isLoading(fqcn) { return !!this.loading[fqcn]; },
            hasResult(fqcn) { return typeof this.results[fqcn] === 'string'; },
            isOpen(fqcn) { return !!this.openResult[fqcn]; },
            resultText(fqcn) { return this.results[fqcn] || ''; },
            toggleResult(fqcn) { this.openResult[fqcn] = !this.openResult[fqcn]; },

            /* ---- clipboard ---- */
            async copy(text, label) {
                try {
                    await navigator.clipboard.writeText(text);
                    window.domoToast((label || 'Copied') + ' to clipboard', 'success', 2500);
                } catch (e) {
                    window.domoToast('Clipboard unavailable in this context.', 'error');
                }
            },
            copyFqcn(fqcn) { this.copy(fqcn, fqcn); },
            copyResult(fqcn) { this.copy(this.results[fqcn] || '', 'AI result'); },

            /* ---- AI analysis (same fetch contract as the Analyze page) ---- */
            async analyze(model) {
                const fqcn = model.fqcn;
                if (this.loading[fqcn]) return;
                this.loading[fqcn] = true;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
                    const response = await fetch('{{ route('domo.analyze.post') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ type: 'relationships', target: fqcn }),
                    });

                    const data = await response.json();

                    if (!response.ok || data.success === false) {
                        window.domoToast(data.message || `Request failed with status ${response.status}.`, 'error');
                        return;
                    }

                    this.results[fqcn] = JSON.stringify(data.result ?? {}, null, 2);
                    this.openResult[fqcn] = true;
                    window.domoToast(`Analysis ready for ${model.base}`, 'success');
                } catch (e) {
                    window.domoToast(e.message || 'Unexpected error while contacting the server.', 'error');
                } finally {
                    this.loading[fqcn] = false;
                }
            },
        };
    }
</script>
@endpush
