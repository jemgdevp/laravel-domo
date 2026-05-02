# Project Structure

```
laravel-domo/
├── bin/                        # Executable scripts
│   ├── serve                   # Web dashboard server launcher
│   └── tui                     # Terminal UI launcher
│
├── config/                     # Package configuration
│   └── domo.php                # Main configuration file
│
├── resources/                  # Views, assets, translations
│   ├── dist/                   # Compiled assets
│   ├── lang/                   # Translation files
│   └── views/
│       └── dashboard/
│           ├── layout.blade.php    # Main layout template
│           ├── index.blade.php     # Dashboard home
│           ├── schema.blade.php    # Schema viewer
│           └── models.blade.php    # Models viewer
│
├── src/
│   ├── Commands/             # Laravel console commands
│   │   ├── DomoServeCommand.php  # Web dashboard server
│   │   └── DomoTuiCommand.php    # Terminal UI
│   │
│   ├── Contracts/            # Interface definitions
│   │   ├── AiDriverInterface.php
│   │   ├── McpServerInterface.php
│   │   └── SchemaAnalyzerInterface.php
│   │
│   ├── Events/               # Event classes
│   │   ├── MigrationGenerated.php
│   │   ├── SchemaAnalysisCompleted.php
│   │   └── SchemaAnalysisStarted.php
│   │
│   ├── Exceptions/           # Custom exceptions
│   │   ├── AiDriverException.php
│   │   ├── DomoException.php
│   │   ├── McpServerException.php
│   │   └── SchemaAnalyzerException.php
│   │
│   ├── Facades/              # Facade classes
│   │   └── Domo.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── DashboardController.php
│   │   └── Routes/
│   │       └── web.php
│   │
│   ├── Listeners/            # Event listeners
│   │   ├── LogMigrationGeneration.php
│   │   └── LogSchemaAnalysis.php
│   │
│   ├── Models/               # Eloquent models (if needed)
│   │
│   ├── Resolvers/            # Custom resolvers
│   │   └── UrlResolver.php
│   │
│   ├── Services/
│   │   ├── AI/               # AI driver implementations
│   │   │   ├── AnthropicDriver.php
│   │   │   └── OpenAIDriver.php
│   │   ├── Database/         # Database services
│   │   │   ├── ConnectionManager.php
│   │   │   └── TableCollector.php
│   │   ├── MCP/              # Model Context Protocol
│   │   │   └── DomoMcpServer.php
│   │   ├── Migration/        # Migration services
│   │   │   ├── MigrationGenerator.php
│   │   │   └── MigrationPreviewer.php
│   │   └── Schema/           # Schema analysis
│   │       └── Analyzer.php
│   │
│   └── DomoServiceProvider.php   # Service provider
│
├── test/
│   ├── Feature/              # Feature tests
│   │   ├── CommandsTest.php
│   │   ├── DashboardTest.php
│   │   └── McpServerTest.php
│   │
│   ├── Fixture/              # Test fixtures
│   ├── Stubs/                # Test stubs
│   ├── Unit/                 # Unit tests
│   │   ├── AiDriverTest.php
│   │   ├── ConnectionManagerTest.php
│   │   ├── McpServerTest.php
│   │   └── SchemaAnalyzerTest.php
│   │
│   └── TestCase.php          # Base test case
│
├── .github/
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.yml
│   │   └── feature_request.yml
│   ├── workflows/
│   │   ├── code-style.yml
│   │   ├── release.yml
│   │   ├── static-analysis.yml
│   │   └── tests.yml
│   └── PULL_REQUEST_TEMPLATE.md
│
├── .editorconfig             # Editor configuration
├── .env.example              # Environment example
├── .gitconfig                # Git configuration
├── .gitignore                # Git ignore rules
├── .gitmessage               # Commit message template
├── .opencodeignore           # Opencode ignore rules
├── .php-cs-fixer.dist.php    # PHP CS Fixer config
├── AGENTS.md                 # AI agent guidelines
├── CHANGELOG.md              # Changelog
├── CODE_OF_CONDUCT.md        # Code of conduct
├── composer.json             # Composer dependencies
├── CONTRIBUTING.md           # Contributing guide
├── LICENSE.md                # License
├── Makefile                  # Makefile commands
├── opencode.json             # Opencode configuration
├── phpcs.xml                 # PHP CodeSniffer config
├── phpstan.neon              # PHPStan config
├── phpunit.xml               # PHPUnit config
├── README.md                 # Project readme
└── SECURITY.md               # Security policy
```
