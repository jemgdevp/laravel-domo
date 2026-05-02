# Laravel Domo

<div align="center">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jemgdevp/laravel-domo.svg?style=flat-square)](https://packagist.org/packages/jemgdevp/laravel-domo)
[![Total Downloads](https://img.shields.io/packagist/dt/jemgdevp/laravel-domo.svg?style=flat-square)](https://packagist.org/packages/jemgdevp/laravel-domo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jemgdevp/laravel-domo/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jemgdevp/laravel-domo/actions)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-blue?style=flat-square)](https://phpstan.org/)
[![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)

</div>

## 🚀 Overview

**Laravel Domo** is an all-in-one database engineering suite for Laravel (v11-v13). It bridges the gap between raw SQL, Eloquent models, and AI-driven architecture.

### ✨ Features

- **🌐 Web Dashboard** - Rich visual interface for database management (`domo:serve`)
- **💻 Terminal UI** - Powerful TUI for command-line workflow (`domo:tui`)
- **🤖 AI Architect** - AI-powered Eloquent ORM analysis and suggestions
- **🔌 MCP Integration** - Model Context Protocol for AI agent connectivity
- **📊 Schema Analyzer** - Intelligent database schema inspection
- **🔄 Migration Management** - Visual migration generation and preview
- **📦 Portability** - Import/Export SQL dumps and migrations

## 📦 Installation

```bash
composer require jemgdevp/laravel-domo
```

### Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=domo-config
```

## 🎯 Usage

### Web Dashboard

Start the web dashboard server:

```bash
php artisan domo:serve
```

Access the dashboard at `http://localhost:8080/domo`

### Terminal UI

Launch the TUI:

```bash
php artisan domo:tui
```

### MCP Server

Enable MCP server for AI agent integration:

```env
DOMO_MCP_ENABLED=true
DOMO_MCP_PORT=3000
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

## 📚 Documentation

Full documentation is available at [https://jemgdevp.github.io/laravel-domo](https://jemgdevp.github.io/laravel-domo)

## 🧪 Testing

```bash
composer test
```

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
