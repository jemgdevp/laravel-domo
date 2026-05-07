@extends('domo::dashboard.layout')

@section('title', 'Dashboard')

@section('content')
<div class="animate-fade-in">
    <!-- Welcome Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold mb-2">👋 Welcome to Laravel Domo</h1>
        <p class="text-muted">Your AI-powered database orchestrator</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">📊 Total Tables</div>
            <div class="stat-value">{{ count($tables) }}</div>
            <div class="stat-change positive">↑ Database ready</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">🔧 Eloquent Models</div>
            <div class="stat-value">{{ count($models) }}</div>
            <div class="stat-change positive">↑ Detected</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">💾 Database</div>
            <div class="stat-value" style="font-size: 1.5rem;">{{ config('database.default') }}</div>
            <div class="stat-change">{{ config('database.connections.' . config('database.default') . '.driver') }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">🤖 AI Status</div>
            <div class="stat-value" style="font-size: 1.5rem;">
                @if(config('domo.ai_driver') === 'openai')
                    <span style="color: var(--color-info);">OpenAI</span>
                @elseif(config('domo.ai_driver') === 'anthropic')
                    <span style="color: var(--color-warning);">Anthropic</span>
                @else
                    <span style="color: var(--text-secondary);">Not configured</span>
                @endif
            </div>
            <div class="stat-change">Driver: {{ config('domo.ai_driver') }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">⚡ Quick Actions</h2>
        </div>
        
        <div style="display: flex; gap: var(--spacing-md); flex-wrap: wrap;">
            <a href="{{ route('domo.schema') }}" class="btn btn-primary">
                <span>📊</span>
                View Schema
            </a>
            
            <a href="{{ route('domo.models') }}" class="btn btn-primary">
                <span>🔧</span>
                View Models
            </a>
            
            <a href="{{ route('domo.analyze') }}" class="btn btn-primary">
                <span>🤖</span>
                AI Analysis
            </a>
            
            <button class="btn btn-primary" onclick="alert('Migration generator coming soon!')">
                <span>📝</span>
                Generate Migration
            </button>
            
            <button class="btn btn-primary" onclick="alert('Export feature coming soon!')">
                <span>📤</span>
                Export SQL
            </button>
        </div>
    </div>

    <!-- Tables Overview -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📋 Database Tables</h2>
            <a href="{{ route('domo.schema') }}" class="btn btn-secondary btn-sm">View All</a>
        </div>
        
        @if(count($tables) > 0)
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Table Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($tables, 0, 5) as $table)
                            <tr>
                                <td>
                                    <code>{{ is_array($table) ? reset($table) : $table }}</code>
                                </td>
                                <td>
                                    <span class="badge badge-success">✓ Active</span>
                                </td>
                                <td>
                                    <a href="{{ route('domo.schema') }}" class="btn btn-secondary btn-sm">
                                        View Schema
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if(count($tables) > 5)
                <p class="text-muted text-sm" style="margin-top: var(--spacing-md);">
                    And {{ count($tables) - 5 }} more tables...
                </p>
            @endif
        @else
            <p class="text-muted">No tables found in database.</p>
        @endif
    </div>

    <!-- Models Overview -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">🔧 Eloquent Models</h2>
            <a href="{{ route('domo.models') }}" class="btn btn-secondary btn-sm">View All</a>
        </div>
        
        @if(count($models) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--spacing-md);">
                @foreach(array_slice($models, 0, 6) as $model)
                    <div style="padding: var(--spacing-md); background: var(--bg-secondary); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <div style="font-weight: 600; margin-bottom: var(--spacing-xs);">
                            {{ class_basename($model) }}
                        </div>
                        <code style="font-size: 0.75rem;">{{ $model }}</code>
                    </div>
                @endforeach
            </div>
            
            @if(count($models) > 6)
                <p class="text-muted text-sm" style="margin-top: var(--spacing-md);">
                    And {{ count($models) - 6 }} more models...
                </p>
            @endif
        @else
            <p class="text-muted">No models found in app/Models directory.</p>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📈 Recent Activity</h2>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
            <div style="display: flex; align-items: center; gap: var(--spacing-sm); padding: var(--spacing-sm); background: var(--bg-secondary); border-radius: var(--radius-md);">
                <span>📊</span>
                <span>Schema analyzed</span>
                <span class="badge badge-info">Just now</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: var(--spacing-sm); padding: var(--spacing-sm); background: var(--bg-secondary); border-radius: var(--radius-md);">
                <span>🔧</span>
                <span>Models detected</span>
                <span class="badge badge-info">Just now</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: var(--spacing-sm); padding: var(--spacing-sm); background: var(--bg-secondary); border-radius: var(--radius-md);">
                <span>🏠</span>
                <span>Dashboard accessed</span>
                <span class="badge badge-info">Now</span>
            </div>
        </div>
    </div>
</div>
@endsection
