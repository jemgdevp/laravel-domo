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
    'ai_driver' => env('DOMO_AI_DRIVER', 'opencode'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    |
    | Each provider describes how to reach an AI backend. The active provider
    | is selected by the "ai_driver" value above (it must match one of the
    | keys below). You can add your own providers — any OpenAI-compatible
    | service (Groq, OpenRouter, Ollama, DeepSeek, ...) works by setting
    | "variant" to the base protocol and pointing "base_url" at its endpoint.
    |
    | Keys per provider:
    |   - variant:  base protocol — "anthropic" speaks the Anthropic API; any
    |               other value ("openai", "opencode", ...) uses the
    |               OpenAI-compatible protocol.
    |   - api_key:  the credential for the service.
    |   - model:    model identifier; null falls back to the driver default.
    |   - base_url: custom endpoint; null uses the official API URL.
    |
    */
    'providers' => [
        'openai' => [
            'variant' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('DOMO_OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('DOMO_OPENAI_BASE_URL'),
        ],

        'anthropic' => [
            'variant' => 'anthropic',
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('DOMO_ANTHROPIC_MODEL', 'claude-sonnet-4-5'),
            'base_url' => env('DOMO_ANTHROPIC_BASE_URL'),
        ],

        'opencode' => [
            'variant' => 'opencode',
            'api_key' => env('DOMO_OPENCODE_API_KEY'),
            'model' => env('DOMO_OPENCODE_MODEL', 'deepseek-v4-pro'),
            'base_url' => env('DOMO_OPENCODE_BASE_URL', 'https://opencode.ai/zen/go/v1/'),
        ],

        // Example of a user-defined, OpenAI-compatible provider:
        // 'groq' => [
        //     'variant' => 'openai',
        //     'api_key' => env('GROQ_API_KEY'),
        //     'model' => 'llama-3.3-70b-versatile',
        //     'base_url' => 'https://api.groq.com/openai/v1',
        // ],
    ],

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
    | Web dashboard configuration for the visual interface. The dashboard
    | auto-registers its routes (no command required) — just visit the
    | configured route in your browser.
    |
    */
    'dashboard' => [
        'enabled' => env('DOMO_DASHBOARD_ENABLED', true),

        /*
        | Environments where the dashboard is allowed to auto-register its
        | routes. Like Telescope, it is a local development tool and stays
        | out of production by default. Set this to an empty array to allow
        | every environment (NOT recommended).
        */
        'environments' => ['local'],

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
