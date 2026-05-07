@extends('domo::dashboard.layout')

@section('title', 'Schema')

@section('content')
<div class="card">
    <h2 class="card-title">📊 Database Schema</h2>
    
    @foreach($schemas as $tableName => $schema)
        <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid #E5E7EB; border-radius: 0.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">
                {{ $tableName }}
            </h3>
            
            @if(isset($schema['columns']))
                <table class="table" style="font-size: 0.875rem;">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schema['columns'] as $column)
                            <tr>
                                <td style="font-weight: 500;">
                                    {{ is_object($column) ? ($column->Field ?? 'N/A') : ($column['Field'] ?? 'N/A') }}
                                </td>
                                <td>
                                    <code style="background: #F3F4F6; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">
                                        {{ is_object($column) ? ($column->Type ?? 'N/A') : ($column['Type'] ?? 'N/A') }}
                                    </code>
                                </td>
                                <td>
                                    {{ is_object($column) ? ($column->Null ?? 'N/A') : ($column['Null'] ?? 'N/A') }}
                                </td>
                                <td>
                                    {{ is_object($column) ? ($column->Key ?? 'N/A') : ($column['Key'] ?? 'N/A') }}
                                </td>
                                <td>
                                    {{ is_object($column) ? ($column->Default ?? 'NULL') : ($column['Default'] ?? 'NULL') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
</div>
@endsection
