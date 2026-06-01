# 🚀 Laravel Domo - Quick Start Guide

## Setup

```bash
# Clone the repository
git clone https://github.com/jemgdevp/laravel-domo.git
cd laravel-domo

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure your API keys
# Edit .env and add (default provider is opencode):
# - DOMO_OPENCODE_API_KEY + DOMO_OPENCODE_BASE_URL  (default)
# - or OPENAI_API_KEY / ANTHROPIC_API_KEY and set DOMO_AI_DRIVER accordingly
```

## Development

```bash
# Run all tests
make test

# Check code style
make lint

# Fix code style issues
make cs-fix

# Run static analysis
make phpstan

# Full build (clean + lint + test + phpstan)
make build
```

## Usage in Laravel Project

```bash
# Install in your Laravel project
composer require jemgdevp/laravel-domo

# Publish configuration
php artisan vendor:publish --tag=domo-config

# Start web dashboard
php artisan domo:serve

# Or use TUI
php artisan domo:tui
```

## Configuration

Edit `config/domo.php`:

```php
return [
    'ai_driver' => 'opencode', // openai | anthropic | opencode | your own
    'mcp' => [
        'enabled' => true,
        'port' => 3000,
    ],
    'dashboard' => [
        'enabled' => true,
        'route' => 'domo',
    ],
];
```

## Testing

```bash
# Run all tests
vendor/bin/phpunit

# Run with coverage
vendor/bin/phpunit --coverage-html=coverage

# Run specific test
vendor/bin/phpunit --filter=test_analyzer_can_be_instantiated
```

## Code Style

```bash
# Check style
vendor/bin/pint --test

# Fix style
vendor/bin/pint

# PHP-CS-Fixer
vendor/bin/php-cs-fixer fix
```

## Static Analysis

```bash
# Run PHPStan
vendor/bin/phpstan analyse

# Run with max level
vendor/bin/phpstan analyse --level=max
```

## Commit Messages

This project uses [Conventional Commits](https://www.conventionalcommits.org/):

```bash
# Format
<type>(<scope>): <description>

# Examples
feat(commands): add domo:serve command
fix(schema): resolve null pointer in analysis
docs(readme): update installation instructions
test(unit): add tests for AI driver
```

## Project Structure

See [STRUCTURE.md](STRUCTURE.md) for detailed project structure.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

## Support

- Documentation: https://jemgdevp.github.io/laravel-domo
- Issues: https://github.com/jemgdevp/laravel-domo/issues
- Email: murksopps@gmail.com
