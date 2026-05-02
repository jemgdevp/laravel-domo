<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | AI Driver
    |--------------------------------------------------------------------------
    |
    | The AI driver to use for schema analysis and migration generation.
    | Supported: "openai", "anthropic"
    |
    */
    'ai_driver' => env('DOMO_AI_DRIVER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    |
    | Configure your preferred AI provider API key.
    |
    */
    'openai_api_key' => env('OPENAI_API_KEY'),
    'anthropic_api_key' => env('ANTHROPIC_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | MCP Server
    |--------------------------------------------------------------------------
    |
    | Model Context Protocol server configuration for AI agent integration.
    |
    */
    'mcp' => [
        'enabled' => env('DOMO_MCP_ENABLED', true),
        'port' => env('DOMO_MCP_PORT', 3000),
        'host' => env('DOMO_MCP_HOST', '127.0.0.1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    |
    | Web dashboard configuration for the visual interface.
    |
    */
    'dashboard' => [
        'enabled' => env('DOMO_DASHBOARD_ENABLED', true),
        'route' => env('DOMO_DASHBOARD_ROUTE', 'domo'),
        'host' => env('DOMO_DASHBOARD_HOST', '127.0.0.1'),
        'port' => env('DOMO_DASHBOARD_PORT', 8080),
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | TUI
    |--------------------------------------------------------------------------
    |
    | Terminal UI configuration for CLI workflow.
    |
    */
    'tui' => [
        'enabled' => env('DOMO_TUI_ENABLED', true),
        'theme' => env('DOMO_TUI_THEME', 'default'),
        'colors' => env('DOMO_TUI_COLORS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Database analysis configuration.
    |
    */
    'database' => [
        'connection' => env('DOMO_DB_CONNECTION', config('database.default')),
        'tables' => [
            'exclude' => env('DOMO_DB_EXCLUDE_TABLES', 'migrations,failed_jobs'),
        ],
    ],
];
