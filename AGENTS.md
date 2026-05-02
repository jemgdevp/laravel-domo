# Laravel Domo - Development Guidelines

## Project Overview
AI-powered database orchestrator for Laravel with Web Dashboard, TUI, and MCP server.

## Architecture

### Directory Structure
```
laravel-domo/
├── bin/                    # Executable scripts
├── config/                 # Package configuration
├── resources/              # Views, assets, translations
├── src/
│   ├── Commands/          # Laravel console commands
│   ├── Facades/           # Facade classes
│   ├── Http/
│   │   ├── Controllers/   # Web dashboard controllers
│   │   └── Routes/        # Route definitions
│   ├── Services/
│   │   ├── AI/            # AI driver implementations
│   │   ├── MCP/           # Model Context Protocol
│   │   └── Schema/        # Schema analysis tools
│   └── DomoServiceProvider.php
├── test/
│   ├── Feature/           # Feature tests
│   ├── Unit/              # Unit tests
│   └── TestCase.php       # Base test case
```

### Key Components

1. **Commands** (`src/Commands/`)
   - `DomoServeCommand` - Web dashboard server
   - `DomoTuiCommand` - Terminal UI

2. **Services** (`src/Services/`)
   - AI Drivers: Anthropic, OpenAI
   - MCP Server for AI agent integration
   - Schema Analyzer for Eloquent models

3. **HTTP Layer** (`src/Http/`)
   - Dashboard controllers
   - Web routes

## Development Standards

### PHP Requirements
- Minimum PHP 8.2
- Laravel 11.x - 13.x compatibility
- PSR-4 autoloading

### Code Style
- Follow Laravel conventions
- Use strict typing where possible
- Document public APIs with PHPDoc

### Testing
- PHPUnit 10.5+ / 11.x
- TestBench for Laravel package testing
- Separate Unit and Feature tests

## Commands

```bash
# Run tests
./vendor/bin/phpunit

# Run specific test group
./vendor/bin/phpunit --group=command-line-url-resolver

# Check code style (if configured)
composer lint
```

## MCP Integration
The package includes MCP server for AI agent connectivity. See `src/Services/MCP/DomoMcpServer.php`.
