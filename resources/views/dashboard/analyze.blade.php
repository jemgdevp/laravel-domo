@extends('domo::dashboard.layout')

@section('title', 'AI Analysis')

@section('content')
<div class="card" x-data="domoAnalyze()">
    <div class="card-header">
        <h2 class="card-title">🤖 AI Analysis</h2>
    </div>

    <form @submit.prevent="run()" style="display: flex; flex-direction: column; gap: var(--spacing-md);">
        <div>
            <label for="analysis-type" style="display: block; font-weight: 600; margin-bottom: var(--spacing-xs);">Analysis type</label>
            <select id="analysis-type" x-model="type" class="btn btn-secondary" style="width: 100%;">
                <option value="schema">Schema</option>
                <option value="models">Models</option>
                <option value="relationships">Relationships</option>
            </select>
        </div>

        <div>
            <label for="analysis-target" style="display: block; font-weight: 600; margin-bottom: var(--spacing-xs);">Target (optional)</label>
            <input id="analysis-target" x-model="target" type="text" class="btn btn-secondary" style="width: 100%;" placeholder="Table or Model name" />
        </div>

        <button class="btn btn-primary" type="submit" :disabled="loading">
            <span x-show="!loading">✨ Run analysis</span>
            <span x-show="loading" x-cloak>⏳ Analyzing…</span>
        </button>
    </form>

    <div x-show="error" x-cloak class="badge badge-warning animate-fade-in" style="display: block; margin-top: var(--spacing-md); padding: var(--spacing-md);">
        <strong>Analysis failed:</strong> <span x-text="error"></span>
    </div>

    <div x-show="result" x-cloak class="animate-fade-in" style="margin-top: var(--spacing-md);">
        <div class="flex items-center justify-between mb-4">
            <span class="badge badge-success">Success</span>
            <span class="text-muted text-sm" x-text="`${meta.type}${meta.target ? ' · ' + meta.target : ''}`"></span>
        </div>
        <div class="table-container">
            <pre style="margin: 0; padding: var(--spacing-md); overflow-x: auto; font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: 0.8125rem; line-height: 1.5; background: var(--bg-tertiary);"><code x-text="result"></code></pre>
        </div>
    </div>

    <p x-show="!result && !error && !loading" class="text-muted text-sm" style="margin-top: var(--spacing-md);">
        Select an analysis type and run it to get AI-powered insights about your database.
    </p>
</div>

<script>
    function domoAnalyze() {
        return {
            type: 'schema',
            target: '',
            loading: false,
            result: '',
            error: '',
            meta: { type: '', target: '' },

            async run() {
                this.loading = true;
                this.error = '';
                this.result = '';

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
                        return;
                    }

                    this.meta = { type: data.type ?? this.type, target: data.target ?? '' };
                    this.result = JSON.stringify(data.result ?? {}, null, 2);
                } catch (e) {
                    this.error = e.message || 'Unexpected error while contacting the server.';
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
@endsection
