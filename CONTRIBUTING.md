# Contributing to Laravel Domo

Thank you for considering contributing to Laravel Domo! We appreciate your interest in making this project better.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Code Standards](#code-standards)
- [Testing](#testing)
- [Pull Requests](#pull-requests)
- [Bug Reports](#bug-reports)
- [Feature Requests](#feature-requests)

## Code of Conduct

This project is open source and as such invites contributions from the community. Please be respectful and considerate of others when contributing.

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/laravel-domo.git`
3. Install dependencies: `composer install`
4. Create a branch: `git checkout -b feature/your-feature-name`

## Development Setup

### Requirements

- PHP 8.2 or higher
- Composer
- Laravel 11.x, 12.x, or 13.x
- SQLite for testing

### Installation

```bash
# Clone the repository
git clone https://github.com/jemgdevp/laravel-domo.git

# Navigate to the project directory
cd laravel-domo

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

## Code Standards

Laravel Domo follows:

- **PSR-12** coding standards
- **PSR-4** autoloading standards
- Laravel package development best practices

### PHP Requirements

- PHP 8.2 minimum
- Strict types: `declare(strict_types=1);`
- PHPDoc for public APIs
- Type hints for parameters and return types

### Code Style

```bash
# Check code style
composer lint

# Fix code style issues
composer cs-fix
```

## Testing

```bash
# Run all tests
composer test

# Run unit tests
composer test-unit

# Run feature tests
composer test-feature

# Run tests with coverage
vendor/bin/phpunit --coverage-html=coverage
```

### Writing Tests

- Unit tests go in `test/Unit/`
- Feature tests go in `test/Feature/`
- Extend `Jemgdevp\Domo\Tests\TestCase`
- Use descriptive test method names: `test_[method]_[scenario]_[expected_result]`

Example:

```php
public function test_analyzer_can_retrieve_tables(): void
{
    $analyzer = new Analyzer();
    $tables = $analyzer->getTables();
    
    $this->assertIsArray($tables);
}
```

## Pull Requests

**Before submitting a PR:**

1. Ensure all tests pass
2. Check code style
3. Update documentation if needed
4. Add tests for new functionality
5. Update CHANGELOG.md

**PR Process:**

1. Create a feature branch from `main`
2. Make your changes
3. Write/update tests
4. Ensure CI passes
5. Submit PR with clear description

**PR Template:**

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
Describe testing performed

## Checklist
- [ ] Code follows PSR-12
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] CHANGELOG updated
```

## Bug Reports

Please include:

- **PHP version**
- **Laravel version**
- **Package version**
- **Steps to reproduce**
- **Expected behavior**
- **Actual behavior**
- **Error messages/stack traces**

## Feature Requests

Feature requests should:

- Be clearly described
- Explain the use case
- Consider backward compatibility
- Be realistic in scope

## Security Issues

If you discover a security vulnerability, please email [murksopps@gmail.com](mailto:murksopps@gmail.com) instead of using the issue tracker.

## Questions?

Open an issue with the "question" label or contact the maintainer.

## License

By contributing to Laravel Domo, you agree that your contributions will be licensed under the MIT License.
