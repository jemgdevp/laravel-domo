@extends('domo::dashboard.layout')

@section('title', 'AI Analysis')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">🤖 AI Analysis</h2>
    </div>

    <form method="POST" action="{{ route('domo.analyze.post') }}" style="display: flex; flex-direction: column; gap: var(--spacing-md);">
        @csrf

        <div>
            <label for="analysis-type" style="display: block; font-weight: 600; margin-bottom: var(--spacing-xs);">Analysis type</label>
            <select id="analysis-type" name="type" class="btn btn-secondary" style="width: 100%;">
                <option value="schema">Schema</option>
                <option value="models">Models</option>
                <option value="relationships">Relationships</option>
            </select>
        </div>

        <div>
            <label for="analysis-target" style="display: block; font-weight: 600; margin-bottom: var(--spacing-xs);">Target (optional)</label>
            <input id="analysis-target" name="target" type="text" class="btn btn-secondary" style="width: 100%;" placeholder="Table or Model name" />
        </div>

        <button class="btn btn-primary" type="submit">
            ✨ Run analysis
        </button>
    </form>

    <p class="text-muted text-sm" style="margin-top: var(--spacing-md);">
        AI integration is in progress. This endpoint validates the request and returns a placeholder response.
    </p>
</div>
@endsection
