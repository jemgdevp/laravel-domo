# Laravel Domo

<div align="center">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jemgdevp/laravel-domo.svg?style=flat-square)](https://packagist.org/packages/jemgdevp/laravel-domo)
[![Total Downloads](https://img.shields.io/packagist/dt/jemgdevp/laravel-domo.svg?style=flat-square)](https://packagist.org/packages/jemgdevp/laravel-domo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jemgdevp/laravel-domo/tests.yml?branch=develop&label=tests&style=flat-square)](https://github.com/jemgdevp/laravel-domo/actions)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-blue?style=flat-square)](https://phpstan.org/)
[![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)

</div>

## 🚀 Overview

**Laravel Domo** is an all-in-one database engineering suite for Laravel (v11+). It bridges the gap between raw SQL, Eloquent models, and AI-driven architecture.

### ✨ Features

- **🌐 Web Dashboard** - Rich visual interface for database management (`domo:serve`)
- **💻 Terminal UI** - Powerful TUI for command-line workflow (`domo:tui`)
- **🤖 AI Architect** - AI-powered Eloquent ORM analysis and suggestions
- **🔌 MCP Integration** - Model Context Protocol for AI agent connectivity
- **📊 Schema Analyzer** - Intelligent database schema inspection
- **🔄 Migration Management** - Visual migration generation and preview
- **📦 Portability** - Import/Export SQL dumps and migrations

## 📦 Installation

Install the package via Composer:

```bash
composer require --dev jemgdevp/laravel-domo
```

### Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=domo-config
```

### Environment Setup

Add your API keys to `.env`:

```env
# AI Driver Configuration
DOMO_AI_DRIVER=openai
OPENAI_API_KEY=your-api-key

# Or use Anthropic
# DOMO_AI_DRIVER=anthropic
# ANTHROPIC_API_KEY=your-api-key

# MCP Server
DOMO_MCP_ENABLED=true
DOMO_MCP_PORT=3000

# Dashboard
DOMO_DASHBOARD_ENABLED=true
DOMO_DASHBOARD_PORT=8080
```

## 🎯 Usage

### Web Dashboard

Start the web dashboard server:

```bash
php artisan domo:serve
```

Access the dashboard at `http://localhost:8080/domo`

**Options:**
```bash
php artisan domo:serve --host=0.0.0.0 --port=3000 --open
```

### Terminal UI

Launch the TUI:

```bash
php artisan domo:tui
```

**Options:**
```bash
php artisan domo:tui --no-colors --simple
```

Note: the rich TUI is supported on Linux/macOS terminals. Windows is not supported by the current PHP-TUI backend.

### MCP Server

Enable MCP server for AI agent integration:

```env
DOMO_MCP_ENABLED=true
DOMO_MCP_PORT=3000
DOMO_MCP_HOST=127.0.0.1
```

### AI Configuration

Configure your preferred AI driver:

```env
DOMO_AI_DRIVER=openai
OPENAI_API_KEY=your-api-key

# Or use Anthropic
# DOMO_AI_DRIVER=anthropic
# ANTHROPIC_API_KEY=your-api-key
```

### Available Commands

| Command | Description |
|---------|-------------|
| `php artisan domo:serve` | Start web dashboard |
| `php artisan domo:tui` | Launch terminal UI |
| `php artisan vendor:publish --tag=domo-config` | Publish config |

### Configuration

Edit `config/domo.php`:

```php
return [
    'ai_driver' => 'openai', // or 'anthropic'
    
    'mcp' => [
        'enabled' => true,
        'port' => 3000,
        'host' => '127.0.0.1',
    ],
    
    'dashboard' => [
        'enabled' => true,
        'route' => 'domo',
        'host' => '127.0.0.1',
        'port' => 8080,
        'middleware' => ['web'],
    ],
    
    'tui' => [
        'enabled' => true,
        'theme' => 'default',
        'colors' => true,
    ],
    
    'database' => [
        'connection' => config('database.default'),
        'tables' => [
            'exclude' => 'migrations,failed_jobs',
        ],
    ],
];
```

## 📚 Documentation

Full documentation is available at [https://jemgdevp.github.io/laravel-domo](https://jemgdevp.github.io/laravel-domo)

### Additional Resources

- [Quick Start Guide](QUICKSTART.md)
- [Project Structure](STRUCTURE.md)
- [Contributing Guide](CONTRIBUTING.md)
- [Security Policy](SECURITY.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)

## 🧪 Testing

```bash
composer test
```

## 🛡️ Security

If you discover any security related issues, please email murksopps@gmail.com instead of using the issue tracker.

## 📊 Requirements

- PHP 8.2+
- Laravel 11.x or 12.x
- Database: MySQL, PostgreSQL, SQLite, or SQL Server

## ❓ FAQ

**Q: Is this package production-ready?**  
A: Laravel Domo is designed for development environments. Install as dev dependency: `composer require --dev jemgdevp/laravel-domo`

**Q: Which AI providers are supported?**  
A: Currently supports OpenAI and Anthropic. More providers coming soon.

**Q: Can I use this with SQLite?**  
A: Yes! Laravel Domo supports MySQL, PostgreSQL, SQLite, and SQL Server.

**Q: How do I customize the dashboard route?**  
A: Edit `config/domo.php` and change the `dashboard.route` value.

## 🤝 Contributing

See our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

Laravel Domo is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [TestBench](https://github.com/orchestral/testbench) - Laravel testing helper
- [Anthropic](https://anthropic.com) - AI provider
- [OpenAI](https://openai.com) - AI provider

<div align="center">

**Made with ❤️ by [jemgdevp](https://github.com/jemgdevp)**

</div>
