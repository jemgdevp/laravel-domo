# Makefile for Laravel Domo
# Modern Laravel Package Development Workflow

.PHONY: help install update test test-unit test-feature test-coverage lint cs-fix phpstan format clean build docs

# Default target
help:
	@echo "🏠 Laravel Domo - Development Commands"
	@echo ""
	@echo "📦 Installation"
	@echo "  make install          Install all dependencies"
	@echo "  make update           Update dependencies"
	@echo ""
	@echo "🧪 Testing"
	@echo "  make test             Run all tests"
	@echo "  make test-unit        Run unit tests"
	@echo "  make test-feature     Run feature tests"
	@echo "  make test-coverage    Run tests with coverage report"
	@echo ""
	@echo "🔍 Code Quality"
	@echo "  make lint             Check code style (Pint)"
	@echo "  make cs-fix           Fix code style issues"
	@echo "  make phpstan          Run static analysis"
	@echo "  make format           Format code with PHP-CS-Fixer"
	@echo ""
	@echo "🧹 Maintenance"
	@echo "  make clean            Clean cache and temp files"
	@echo "  make build            Prepare for production"
	@echo ""
	@echo "📚 Documentation"
	@echo "  make docs             Generate documentation"

# 📦 Installation
install:
	@echo "📦 Installing dependencies..."
	composer install --no-interaction --prefer-dist

update:
	@echo "🔄 Updating dependencies..."
	composer update --no-interaction --prefer-dist

# 🧪 Testing
test:
	@echo "🧪 Running tests..."
	vendor/bin/phpunit --colors=always

test-unit:
	@echo "🧪 Running unit tests..."
	vendor/bin/phpunit --testsuite="Domo Test Suite" --testdox --colors=always --filter="Unit"

test-feature:
	@echo "🧪 Running feature tests..."
	vendor/bin/phpunit --testsuite="Domo Test Suite" --testdox --colors=always --filter="Feature"

test-coverage:
	@echo "🧪 Running tests with coverage..."
	vendor/bin/phpunit --coverage-html=coverage --colors=always
	@echo "📊 Coverage report generated in coverage/index.html"

# 🔍 Code Quality
lint:
	@echo "🔍 Checking code style..."
	vendor/bin/pint --test

cs-fix:
	@echo "♻️  Fixing code style..."
	vendor/bin/pint

phpstan:
	@echo "🔍 Running static analysis..."
	vendor/bin/phpstan analyse --no-progress

format:
	@echo "✨ Formatting code..."
	vendor/bin/php-cs-fixer fix --verbose

# 🧹 Maintenance
clean:
	@echo "🧹 Cleaning cache and temp files..."
	rm -rf vendor/phpunit/php-code-coverage/.phpunit.cache/
	rm -rf .phpunit.cache/
	rm -rf coverage/
	find . -name "*.log" -delete
	find . -name "*.cache" -delete
	@echo "✨ Clean complete!"

build: clean lint test phpstan
	@echo "🏗️  Building for production..."
	@echo "✨ Build complete!"

# 📚 Documentation
docs:
	@echo "📚 Generating documentation..."
	@echo "Documentation generation coming soon!"

# 🎯 Quick setup for new contributors
setup: install
	@echo "✨ Setup complete! Run 'make test' to verify everything works."
