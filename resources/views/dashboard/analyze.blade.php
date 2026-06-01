@extends('domo::dashboard.layout')

@section('title', 'AI Analysis')

@php
    /*
     | The controller passes the RAW driver result for $tables: an array of
     | stdClass row objects whose single column name varies per database
     | driver (MySQL "Tables_in_db", pgsql "tablename", sqlite "name",
     | sqlsrv "TABLE_NAME"). e() throws on a stdClass, so we normalize every
     | row to a plain table-name string here — mirroring index.blade.php —
     | before any output or @js() usage.
     */
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
@endphp

@push('styles')
<style>
    /* ── AI Analysis page tweaks (compose design-system tokens only) ── */
    .analyze-grid { display: grid; grid-template-columns: minmax(0, 340px) minmax(0, 1fr); gap: var(--space-5); align-items: start; }
    @media (max-width: 880px) { .analyze-grid { grid-template-columns: 1fr; } }

    /* Visually-hidden helper (no sr-only utility exists in the design system).
       Keeps the "(required)" text in the accessible name without showing it. */
    .domo-vh {
        position: absolute; width: 1px; height: 1px;
        padding: 0; margin: -1px; overflow: hidden;
        clip: rect(0 0 0 0); clip-path: inset(50%); white-space: nowrap; border: 0;
    }

    /* Error surface variant, kept page-scoped (tokens.blade.php is owned elsewhere).
       Token-based so it stays theme-safe and matches the toast is-error treatment. */
    .card.is-error { border-color: var(--error); background: var(--error-soft); }

    /* Recursive result tree, built from design tokens */
    .result-tree { font-size: 0.875rem; line-height: 1.6; }
    .result-node { margin: 0; }
    .result-row {
        display: grid;
        grid-template-columns: minmax(120px, 220px) 1fr;
        gap: var(--space-4);
        padding: var(--space-3) 0;
        border-bottom: 1px solid var(--border-subtle);
    }
    @media (max-width: 540px) { .result-row { grid-template-columns: 1fr; gap: var(--space-1); } }
    .result-row:last-child { border-bottom: none; }
    .result-key {
        font-family: var(--font-mono); font-size: 0.78rem; font-weight: 600;
        color: var(--text-secondary); letter-spacing: 0.01em;
        word-break: break-word; padding-top: 1px;
    }
    .result-val { min-width: 0; color: var(--text-primary); }
    .result-val.is-prose { white-space: pre-wrap; word-break: break-word; }
    .result-nested {
        border-left: 1px solid var(--border-subtle);
        padding-left: var(--space-4);
        margin-top: var(--space-2);
    }
    .result-list { list-style: none; display: flex; flex-direction: column; gap: var(--space-2); }
    .result-list-item {
        display: flex; align-items: flex-start; gap: var(--space-3);
        padding: var(--space-2) 0;
        border-bottom: 1px solid var(--border-subtle);
    }
    .result-list-item:last-child { border-bottom: none; }
    .result-list-bullet {
        flex-shrink: 0; margin-top: 0.5em;
        width: 5px; height: 5px; border-radius: 50%;
        background: var(--accent); box-shadow: 0 0 6px var(--accent-glow);
    }
    .result-list-body { min-width: 0; flex: 1; }
    .result-empty-val { color: var(--text-muted); font-style: italic; font-size: 0.8rem; }

    /* Segmented view toggle (Rich / Raw) */
    .seg { display: inline-flex; padding: 3px; gap: 3px; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); }
    .seg-btn {
        appearance: none; border: none; cursor: pointer;
        font-family: var(--font-mono); font-size: 0.72rem; font-weight: 600;
        padding: var(--space-1) var(--space-3); height: 26px;
        border-radius: var(--radius-sm); background: transparent; color: var(--text-secondary);
        transition: background var(--t-fast), color var(--t-fast);
    }
    .seg-btn:hover { color: var(--text-primary); }
    .seg-btn.is-on { background: var(--accent-soft); color: var(--accent); box-shadow: inset 0 0 0 1px var(--accent-soft-hover); }

    /* Suggestion chip strip */
    .suggest-strip { display: flex; flex-wrap: wrap; gap: var(--space-2); }
</style>
@endpush

@section('content')
<div x-data="domoAnalyze()" x-init="init()">
    <div class="page-header">
        <div class="page-eyebrow">Workspace · AI</div>
        <h1 class="page-title">AI Analysis</h1>
        <p class="page-subtitle">
            Ask the configured AI driver to inspect your schema, models, and relationships —
            then read the result as a structured, copyable report.
        </p>
    </div>

    <div class="analyze-grid">
        {{-- ───────────────────────── REQUEST PANEL ───────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3v18M3 12h18"/><circle cx="12" cy="12" r="9"/>
                    </svg>
                    Run analysis
                </h2>
            </div>

            <form @submit.prevent="run()" novalidate>
                <div class="field">
                    <label class="field-label" for="analysis-type">Analysis type <span class="req" aria-hidden="true">*</span><span class="domo-vh"> (required)</span></label>
                    <select id="analysis-type" class="select" x-model="type" :disabled="loading" required aria-required="true">
                        <option value="schema">Schema</option>
                        <option value="models">Models</option>
                        <option value="relationships">Relationships</option>
                    </select>
                    <span class="field-hint" x-text="typeHint"></span>
                </div>

                <div class="field">
                    <label class="field-label" for="analysis-target">Target</label>
                    <input
                        id="analysis-target"
                        class="input is-mono"
                        type="text"
                        x-model="target"
                        :disabled="loading"
                        list="domo-target-list"
                        autocomplete="off"
                        spellcheck="false"
                        placeholder="users"
                    />
                    <datalist id="domo-target-list">
                        @foreach ($tableNames as $tableName)
                            <option value="{{ $tableName }}"></option>
                        @endforeach
                    </datalist>
                    <span class="field-hint">Optional. Leave blank to analyze everything.</span>
                </div>

                @if (count($tableNames))
                    <div class="field">
                        <span class="field-label" id="suggest-label">Quick targets</span>
                        <div class="suggest-strip" role="group" aria-labelledby="suggest-label">
                            @foreach (array_slice($tableNames, 0, 8) as $tableName)
                                <button
                                    type="button"
                                    class="chip"
                                    :class="{ 'is-accent': target === @js($tableName) }"
                                    @click="target = @js($tableName)"
                                    :disabled="loading"
                                >{{ $tableName }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <hr class="divider">

                <button class="btn btn-primary btn-lg w-full" type="submit" :disabled="loading">
                    <span class="spinner" x-show="loading" x-cloak aria-hidden="true"></span>
                    <svg x-show="!loading" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 3l14 9-14 9V3z"/>
                    </svg>
                    <span x-text="loading ? 'Analyzing…' : 'Run analysis'"></span>
                </button>

                <p class="field-hint mt-3 text-center">
                    Press <kbd class="kbd">Enter</kbd> to submit · results stream below.
                </p>
            </form>
        </div>

        {{-- ───────────────────────── RESULT PANEL ───────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    Result
                </h2>
                <div class="card-actions">
                    {{-- State badge --}}
                    <template x-if="loading">
                        <span class="badge badge-info has-pulse"><span class="dot"></span> Running</span>
                    </template>
                    <template x-if="!loading && hasResult">
                        <span class="badge badge-success"><span class="dot"></span> Success</span>
                    </template>
                    <template x-if="!loading && error">
                        <span class="badge badge-error"><span class="dot"></span> Failed</span>
                    </template>

                    {{-- Result-only controls --}}
                    <template x-if="hasResult && !loading">
                        <div class="flex items-center gap-2">
                            <div class="seg" role="group" aria-label="Result view">
                                <button type="button" class="seg-btn"
                                    :class="{ 'is-on': view === 'rich' }" :aria-pressed="view === 'rich'"
                                    @click="view = 'rich'">Rich</button>
                                <button type="button" class="seg-btn"
                                    :class="{ 'is-on': view === 'raw' }" :aria-pressed="view === 'raw'"
                                    @click="view = 'raw'">Raw JSON</button>
                            </div>
                            <button type="button" class="btn-icon is-bordered btn-sm" @click="copyResult()" aria-label="Copy result JSON">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Result meta line --}}
            <div class="flex items-center gap-2 flex-wrap mb-4" x-show="hasResult && !loading" x-cloak>
                <span class="badge badge-primary" x-text="meta.type"></span>
                <template x-if="meta.target">
                    <span class="chip mono" x-text="meta.target"></span>
                </template>
                <template x-if="!meta.target">
                    <span class="text-muted text-xs mono">all targets</span>
                </template>
            </div>

            {{-- Concise live region: announces only the state string, never the
                 full result tree (SC 4.1.3 — status messages stay terse). --}}
            <div class="domo-vh" aria-live="polite" role="status" x-text="liveStatus"></div>

            {{-- LOADING SKELETON --}}
            <div x-show="loading" x-cloak class="animate-fade-in">
                <div class="skeleton is-title mb-4" style="width: 55%"></div>
                <div class="skeleton is-text" style="width: 90%"></div>
                <div class="skeleton is-text" style="width: 80%"></div>
                <div class="skeleton is-text" style="width: 86%"></div>
                <div class="skeleton is-block mt-4"></div>
                <div class="skeleton is-text mt-4" style="width: 70%"></div>
                <div class="skeleton is-text" style="width: 60%"></div>
            </div>

            {{-- ERROR CARD — role="alert" so failures are announced promptly. --}}
            <div x-show="!loading && error" x-cloak class="animate-fade-in" role="alert">
                <div class="card is-error" style="margin-bottom: 0;">
                    <div class="flex items-start gap-3">
                        <span class="text-error" aria-hidden="true" style="flex-shrink:0; margin-top:2px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </span>
                        <div class="flex-1" style="min-width:0;">
                            <div class="font-semibold text-error mb-2">Analysis failed</div>
                            <p class="text-sm text-secondary" style="word-break: break-word;" x-text="error"></p>
                            <button type="button" class="btn btn-secondary btn-sm mt-4" @click="run()">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                                </svg>
                                Retry
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUCCESS — RICH VIEW (rendered normally, outside the live region) --}}
            <div x-show="!loading && hasResult && view === 'rich'" x-cloak class="animate-fade-in result-tree">
                {{-- Recursive renderer: builds an escaped HTML string from the result tree. --}}
                <div x-html="renderValue(result)"></div>
            </div>

            {{-- SUCCESS — RAW JSON VIEW --}}
            <div x-show="!loading && hasResult && view === 'raw'" x-cloak class="animate-fade-in">
                <pre class="code-block"><code x-text="rawJson"></code></pre>
            </div>

            {{-- EMPTY / INITIAL STATE --}}
            <div x-show="!loading && !error && !hasResult" x-cloak class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3v18M3 12h18"/><circle cx="12" cy="12" r="9"/>
                    </svg>
                </div>
                <div class="empty-title">No analysis yet</div>
                <p class="empty-hint">
                    Pick an analysis type, optionally narrow it to a single target, and hit
                    <strong class="text-secondary">Run analysis</strong> to get AI-powered insights about your database.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function domoAnalyze() {
        return {
            type: 'schema',
            target: '',
            loading: false,
            result: null,
            error: '',
            meta: { type: '', target: '' },
            view: 'rich',
            liveStatus: '',

            init() {
                // Register palette quick-actions for each analysis type.
                window.dispatchEvent(new CustomEvent('domo-palette:register', {
                    detail: [
                        { id: 'ai-run-schema', label: 'Analyze: Schema', group: 'AI Analysis', hint: 'run',
                          action: () => this.runType('schema') },
                        { id: 'ai-run-models', label: 'Analyze: Models', group: 'AI Analysis', hint: 'run',
                          action: () => this.runType('models') },
                        { id: 'ai-run-rel', label: 'Analyze: Relationships', group: 'AI Analysis', hint: 'run',
                          action: () => this.runType('relationships') },
                    ],
                }));
            },

            get hasResult() {
                return this.result !== null && this.result !== undefined;
            },
            get rawJson() {
                return JSON.stringify(this.result ?? {}, null, 2);
            },
            get typeHint() {
                return {
                    schema: 'Inspect table columns, types, and indexes.',
                    models: 'Analyze Eloquent model definitions.',
                    relationships: 'Suggest model relationships.',
                }[this.type] || '';
            },

            runType(t) {
                this.type = t;
                this.run();
            },

            async run() {
                if (this.loading) return;
                this.loading = true;
                this.error = '';
                this.result = null;
                this.liveStatus = 'Analysis running';

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
                        body: JSON.stringify({ type: this.type, target: this.target || null }),
                    });

                    const data = await response.json();

                    if (!response.ok || data.success === false) {
                        this.error = data.message || `Request failed with status ${response.status}.`;
                        this.liveStatus = 'Analysis failed: ' + this.error;
                        window.domoToast(this.error, 'error', 0);
                        return;
                    }

                    this.meta = { type: data.type ?? this.type, target: data.target ?? '' };
                    this.result = data.result ?? {};
                    this.view = 'rich';
                    this.liveStatus = 'Analysis complete';
                    window.domoToast('Analysis complete', 'success');
                } catch (e) {
                    this.error = e.message || 'Unexpected error while contacting the server.';
                    this.liveStatus = 'Analysis failed: ' + this.error;
                    window.domoToast(this.error, 'error', 0);
                } finally {
                    this.loading = false;
                }
            },

            async copyResult() {
                const text = this.rawJson;
                try {
                    await navigator.clipboard.writeText(text);
                    window.domoToast('Result copied to clipboard', 'success');
                } catch (e) {
                    // Fallback for non-secure contexts.
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                        window.domoToast('Result copied to clipboard', 'success');
                    } catch (_) {
                        window.domoToast('Could not copy result', 'error');
                    }
                    document.body.removeChild(ta);
                }
            },

            /* ─────────────────────────────────────────────────────────────
               Recursive, XSS-safe renderer. Returns an HTML string built
               only from escaped values + design-system classes.
               ───────────────────────────────────────────────────────────── */
            esc(v) {
                return String(v)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            },

            humanizeKey(k) {
                if (/^\d+$/.test(String(k))) return '#' + k;
                return this.esc(String(k).replace(/[_-]+/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase()));
            },

            // Heuristic: does a string look like code/identifier (mono) vs prose?
            looksLikeCode(s) {
                if (s.length > 80 || /\s\w+\s\w+\s\w+/.test(s)) return false;
                return /[_\\\/$]|::|->|[A-Z][a-z]+[A-Z]|^\w+$/.test(s);
            },

            renderScalar(v) {
                if (v === null || v === undefined) {
                    return '<span class="result-empty-val">null</span>';
                }
                if (typeof v === 'boolean') {
                    const cls = v ? 'badge-success' : 'badge-muted';
                    return `<span class="badge ${cls}"><span class="dot"></span> ${v ? 'true' : 'false'}</span>`;
                }
                if (typeof v === 'number') {
                    return `<span class="mono text-accent">${this.esc(v)}</span>`;
                }
                const s = String(v);
                if (s.trim() === '') {
                    return '<span class="result-empty-val">(empty)</span>';
                }
                if (this.looksLikeCode(s)) {
                    return `<code>${this.esc(s)}</code>`;
                }
                return `<span class="result-val is-prose">${this.esc(s)}</span>`;
            },

            isPlainObject(v) {
                return v !== null && typeof v === 'object' && !Array.isArray(v);
            },

            renderValue(v) {
                // Arrays → list (of scalars) or nested rows (of objects)
                if (Array.isArray(v)) {
                    if (v.length === 0) {
                        return '<span class="result-empty-val">empty list</span>';
                    }
                    const allScalar = v.every((x) => x === null || typeof x !== 'object');
                    let out = '<ul class="result-list">';
                    v.forEach((item) => {
                        out += '<li class="result-list-item"><span class="result-list-bullet" aria-hidden="true"></span>'
                            + '<div class="result-list-body">'
                            + (allScalar ? this.renderScalar(item) : this.renderValue(item))
                            + '</div></li>';
                    });
                    out += '</ul>';
                    return out;
                }

                // Objects → key/value rows
                if (this.isPlainObject(v)) {
                    const keys = Object.keys(v);
                    if (keys.length === 0) {
                        return '<span class="result-empty-val">empty</span>';
                    }
                    let out = '<div class="result-node">';
                    keys.forEach((k) => {
                        const child = v[k];
                        const nested = (this.isPlainObject(child) && Object.keys(child).length > 0)
                            || (Array.isArray(child) && child.some((x) => x !== null && typeof x === 'object'));
                        out += '<div class="result-row">'
                            + `<div class="result-key">${this.humanizeKey(k)}</div>`
                            + '<div class="result-val">'
                            + (nested
                                ? `<div class="result-nested">${this.renderValue(child)}</div>`
                                : this.renderValue(child))
                            + '</div></div>';
                    });
                    out += '</div>';
                    return out;
                }

                // Scalar
                return this.renderScalar(v);
            },
        };
    }
</script>
@endpush
