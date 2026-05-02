@extends('domo::dashboard.layout')

@section('title', 'Models')

@section('content')
<div class="card">
    <h2 class="card-title">🔧 Eloquent Models</h2>
    
    @if(count($models) > 0)
        @foreach($models as $model)
            <div style="margin-bottom: 1rem; padding: 1rem; border: 1px solid #E5E7EB; border-radius: 0.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600;">
                        {{ class_basename($model) }}
                    </h3>
                    <code style="background: #F3F4F6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                        {{ $model }}
                    </code>
                </div>
                
                @if(isset($relationships[$model]) && count($relationships[$model]) > 0)
                    <div style="margin-top: 0.5rem;">
                        <span style="font-size: 0.875rem; color: #6B7280;">Relationships:</span>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.25rem;">
                            @foreach($relationships[$model] as $method => $relation)
                                <span style="background: #EEF2FF; color: #4F46E5; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                    {{ $method }} → {{ class_basename($relation) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p style="color: #6B7280; font-size: 0.875rem;">No relationships detected</p>
                @endif
            </div>
        @endforeach
    @else
        <p style="color: #6B7280;">No models found in app/Models directory.</p>
    @endif
</div>

<div class="card">
    <h2 class="card-title">🤖 AI Suggestions</h2>
    <p style="color: #6B7280;">
        Click below to get AI-powered suggestions for model relationships and improvements.
    </p>
    <button class="btn btn-primary" style="margin-top: 0.5rem;">
        ✨ Analyze Models with AI
    </button>
</div>
@endsection
