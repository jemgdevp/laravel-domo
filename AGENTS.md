# Laravel Domo - AI Agent Guidelines

## [ESTÁTICO] Project Context

### Framework & Requirements
- **Package**: `jemgdevp/laravel-domo`
- **Type**: Laravel Package (library)
- **PHP**: 8.2+ (strict types required)
- **Laravel**: 11.x - 13.x compatibility
- **Autoloading**: PSR-4 (`Jemgdevp\Domo\` → `src/`)

### Architecture Overview
```
laravel-domo/
├── src/                    # Source code
│   ├── Commands/          # Artisan commands
│   ├── Contracts/         # Interfaces
│   ├── Events/            # Event classes
│   ├── Exceptions/        # Custom exceptions
│   ├── Facades/           # Laravel facades
│   ├── Http/              # Controllers & Routes
│   ├── Listeners/         # Event listeners
│   ├── Resolvers/         # Custom resolvers
│   └── Services/          # Business logic
├── test/                   # Tests (Unit & Feature)
├── config/                 # Package config
└── resources/              # Views & assets
```

---

## [ESTÁTICO] Code Standards

### PHP Rules
1. **Strict Types**: Always `declare(strict_types=1);`
2. **Type Hints**: All parameters and return types declared
3. **PHPDoc**: Document all public methods with `@param` and `@return`
4. **PSR-12**: Follow Laravel/PSR-12 coding standards
5. **Naming**: Descriptive, meaningful names (no abbreviations)

### Laravel Conventions
1. **Service Provider**: Register bindings in `DomoServiceProvider`
2. **Contracts**: Define interfaces in `Contracts/` namespace
3. **Exceptions**: Extend `DomoException` base class
4. **Tests**: Use TestBench, extend `Jemgdevp\Domo\Tests\TestCase`
5. **Config**: Use `config('domo.*')` pattern

### Commit Format (Conventional Commits)
```
<type>(<scope>): <description>

feat(commands): add domo:serve command
fix(schema): resolve null pointer in analysis
docs(readme): update installation instructions
test(unit): add tests for AI driver interface
refactor(services): extract schema analysis logic
```

---

## [ESTÁTICO] Code Templates

### Service Class
```php
<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\[Name];

use Jemgdevp\Domo\Contracts\[Interface];

class [ServiceName]
{
    public function __construct(
        protected [Interface] $dependency
    ) {
    }

    /**
     * Method description.
     *
     * @param  Type  $param
     * @return Type
     */
    public function method(Type $param): Type
    {
        // Implementation
    }
}
```

### Test Class
```php
<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Jemgdevp\Domo\Tests\TestCase;

class [ServiceTest] extends TestCase
{
    public function test_method_does_something(): void
    {
        // Arrange
        $service = new Service();

        // Act
        $result = $service->method();

        // Assert
        $this->assertEquals(expected, $result);
    }
}
```

### Exception Class
```php
<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Exceptions;

class [NameException] extends DomoException
{
    public static function create(string $message): static
    {
        return new static($message);
    }
}
```

---

## [DINÁMICO] Current Components

### Commands (`src/Commands/`)
| Command | Class | Description |
|---------|-------|-------------|
| `domo:serve` | `DomoServeCommand` | Run dashboard on a dedicated server (optional; auto-mounts at `/domo` in local) |
| `domo:tui` | `DomoTuiCommand` | Launch terminal UI |

### Services (`src/Services/`)
| Service | Path | Description |
|---------|------|-------------|
| AI Drivers | `Services/AI/` | `AiDriverFactory`, `OpenAIDriver`, `AnthropicDriver` |
| MCP Server | `Services/MCP/DomoMcpServer.php` | Model Context Protocol server |
| Schema | `Services/Schema/Analyzer.php` | Database schema analysis |
| Database | `Services/Database/` | `ConnectionManager`, `TableCollector` |
| Migration | `Services/Migration/` | `MigrationGenerator`, `MigrationPreviewer` |
| Dashboard | `Services/Dashboard/` | `DashboardServer`, `DashboardConfig` |
| TUI | `Services/TUI/` | `DomoTuiApp`, `Screens/*`, `Components/ChoiceFieldComponent`, `Widgets/*` |

### Contracts (`src/Contracts/`)
| Interface | Methods |
|-----------|---------|
| `AiDriverInterface` | `analyzeSchema()`, `generateMigration()`, `suggestRelationships()` |
| `McpServerInterface` | MCP server operations |
| `SchemaAnalyzerInterface` | Schema analysis operations |

### Events (`src/Events/`)
| Event | Trigger |
|-------|---------|
| `SchemaAnalysisStarted` | Before schema analysis |
| `SchemaAnalysisCompleted` | After schema analysis |
| `MigrationGenerated` | After migration generation |

### Exceptions (`src/Exceptions/`)
| Exception | Usage |
|-----------|-------|
| `DomoException` | Base exception class |
| `AiDriverException` | AI driver errors |
| `McpServerException` | MCP server errors |
| `SchemaAnalyzerException` | Schema analysis errors |

### Listeners (`src/Listeners/`)
| Listener | Event |
|----------|-------|
| `LogSchemaAnalysis` | `SchemaAnalysisStarted`, `SchemaAnalysisCompleted` |
| `LogMigrationGeneration` | `MigrationGenerated` |

---

## [DINÁMICO] Configuration

### Config File: `config/domo.php`

```php
return [
    'ai_driver' => env('DOMO_AI_DRIVER', 'opencode'),    // default provider key (see 'providers')

    // variant: 'anthropic' = Anthropic API; anything else = OpenAI-compatible.
    // Resolved by Services/AI/AiDriverFactory; selectable per-analysis in the dashboard.
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
        'opencode' => [                                  // default
            'variant' => 'opencode',
            'api_key' => env('DOMO_OPENCODE_API_KEY'),
            'model' => env('DOMO_OPENCODE_MODEL', 'deepseek-v4-pro'),
            'base_url' => env('DOMO_OPENCODE_BASE_URL'),
        ],
    ],

    'mcp' => [
        'enabled' => env('DOMO_MCP_ENABLED', true),
        'port' => env('DOMO_MCP_PORT', 3000),
        'host' => env('DOMO_MCP_HOST', '127.0.0.1'),
    ],

    'dashboard' => [
        'enabled' => env('DOMO_DASHBOARD_ENABLED', true),
        'environments' => ['local'],  // auto-registers routes only here (never production)
        'route' => env('DOMO_DASHBOARD_ROUTE', 'domo'),
        'host' => env('DOMO_DASHBOARD_HOST', '127.0.0.1'),
        'port' => env('DOMO_DASHBOARD_PORT', 8080),
        'middleware' => ['web'],
    ],

    'tui' => [
        'enabled' => env('DOMO_TUI_ENABLED', true),
        'theme' => env('DOMO_TUI_THEME', 'default'),
        'colors' => env('DOMO_TUI_COLORS', true),
    ],

    'database' => [
        'connection' => env('DOMO_DB_CONNECTION', config('database.default')),
        'tables' => [
            'exclude' => env('DOMO_DB_EXCLUDE_TABLES', 'migrations,failed_jobs'),
        ],
    ],
];
```

### Environment Variables
| Variable | Default | Description |
|----------|---------|-------------|
| `DOMO_AI_DRIVER` | `opencode` | Active provider key (`openai` / `anthropic` / `opencode`) |
| `OPENAI_API_KEY` | - | OpenAI API key |
| `ANTHROPIC_API_KEY` | - | Anthropic API key |
| `DOMO_OPENCODE_API_KEY` | - | opencode API key (default provider) |
| `DOMO_OPENCODE_MODEL` | `deepseek-v4-pro` | opencode model id |
| `DOMO_OPENCODE_BASE_URL` | - | opencode (OpenAI-compatible) endpoint |
| `DOMO_MCP_ENABLED` | `true` | Enable MCP server |
| `DOMO_MCP_PORT` | `3000` | MCP server port |
| `DOMO_MCP_HOST` | `127.0.0.1` | MCP server host |
| `DOMO_DASHBOARD_ENABLED` | `true` | Enable web dashboard |
| `DOMO_DASHBOARD_ROUTE` | `domo` | Dashboard route prefix |
| `DOMO_DASHBOARD_PORT` | `8080` | Dashboard port |
| `DOMO_TUI_ENABLED` | `true` | Enable TUI |
| `DOMO_TUI_THEME` | `default` | TUI theme |
| `DOMO_TUI_COLORS` | `true` | Enable TUI colors |
| `DOMO_DB_CONNECTION` | default | Database connection |
| `DOMO_DB_EXCLUDE_TABLES` | `migrations,failed_jobs` | Tables to exclude |

---

## [DINÁMICO] Available Commands

### Composer Scripts
```bash
composer test          # Run PHPUnit tests
composer test-coverage # Run tests with coverage
composer lint          # Check code style (Pint)
composer cs-fix        # Fix code style
composer phpstan       # Run static analysis
composer format        # Run PHP-CS-Fixer
```

### Artisan Commands
```bash
php artisan domo:serve --host=0.0.0.0 --port=3000 --open
php artisan domo:tui --no-colors --simple
php artisan vendor:publish --tag=domo-config
php artisan vendor:publish --tag=domo-views
```

---

## [ESTÁTICO] Security Rules

### API Keys & Secrets
1. **Never commit**: API keys, credentials, secrets
2. **Environment**: Use `env()` only in config files
3. **Storage**: Use `.env` for local, environment variables for production

### Input/Output
1. **Validation**: Validate all user inputs
2. **XSS Protection**: Escape output in views (`{{ }}` not `{!! !!}`)
3. **CSRF**: Use `@csrf` in forms
4. **SQL**: Use Eloquent/Query Builder (no raw SQL)

### Error Handling
1. **Custom Exceptions**: Use domain-specific exceptions
2. **No Exposure**: Never expose internal details to users
3. **Logging**: Log errors with context

---

## [ESTÁTICO] Testing Requirements

### Structure
- **Unit Tests**: `test/Unit/` - Test individual classes in isolation
- **Feature Tests**: `test/Feature/` - Test integration points

### Conventions
1. **Naming**: `test_[method]_[scenario]_[expected_result]`
2. **Assertions**: Use specific assertions (not `assertTrue`)
3. **Coverage**: Aim for 80%+ coverage
4. **Isolation**: Each test independent, no shared state

### TestBench Setup
```php
use Jemgdevp\Domo\Tests\TestCase;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [DomoServiceProvider::class];
    }
}
```

---

## [ESTÁTICO] AI-Assisted Tasks

### When Generating Code
1. Check existing patterns in codebase
2. Follow established naming conventions
3. Add appropriate type hints
4. Include error handling
5. Write tests for new functionality

### When Refactoring
1. Preserve existing behavior
2. Update tests if needed
3. Maintain backward compatibility
4. Document breaking changes

### When Debugging
1. Check logs first
2. Review test failures
3. Use descriptive error messages
4. Consider edge cases

---

## [ESTÁTICO] Quick Reference

### File Locations
| Type | Location |
|------|----------|
| Services | `src/Services/` |
| Contracts | `src/Contracts/` |
| Commands | `src/Commands/` |
| Tests | `test/Unit/`, `test/Feature/` |
| Config | `config/domo.php` |
| Views | `resources/views/` |

### Key Interfaces
| Interface | Purpose |
|-----------|---------|
| `AiDriverInterface` | AI provider abstraction |
| `McpServerInterface` | MCP server abstraction |
| `SchemaAnalyzerInterface` | Schema analysis abstraction |

### Base Classes
| Class | Purpose |
|-------|---------|
| `DomoException` | Base exception |
| `TestCase` | Base test case |
| `DomoServiceProvider` | Service provider |
