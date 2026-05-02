# Laravel Domo - Cursor IDE Configuration

## Setup

1. **Install Recommended Extensions**
   - Open Command Palette (`Cmd+Shift+P`)
   - Select "Extensions: Show Recommended Extensions"
   - Install all recommended extensions

2. **Configure AI Provider**
   - Open Cursor Settings (`Cmd+,`)
   - Go to AI tab
   - Configure your preferred AI provider (OpenAI, Anthropic, etc.)

3. **Enable Laravel Intellisense**
   - Install PHP Intelephense
   - Install Laravel Artisan extension
   - Install Blade Snippets

## Keyboard Shortcuts

### AI Commands
| Shortcut | Action |
|----------|--------|
| `Cmd+K` | Chat with AI |
| `Cmd+L` | Inline edit |
| `Cmd+Shift+T` | Generate test |
| `Cmd+Shift+E` | Explain code |
| `Cmd+Shift+R` | Refactor |
| `Cmd+Shift+F` | Fix bugs |
| `Cmd+Shift+O` | Optimize code |

### General
| Shortcut | Action |
|----------|--------|
| `Cmd+P` | Quick open file |
| `Cmd+Shift+O` | Go to symbol |
| `Cmd+T` | Find in workspace |
| `F12` | Go to definition |
| `Shift+F12` | Go to implementation |
| `Cmd+D` | Select next occurrence |

## AI-Assisted Development

### Generate Test
1. Open a PHP file
2. Press `Cmd+Shift+T`
3. AI will generate test cases based on the code

### Explain Code
1. Select code block
2. Press `Cmd+Shift+E`
3. AI explains what the code does

### Refactor
1. Select code to refactor
2. Press `Cmd+Shift+R`
3. Describe the refactoring you want
4. AI suggests improvements

### Fix Bugs
1. Select problematic code
2. Press `Cmd+Shift+F`
3. AI analyzes and suggests fixes

## Best Practices

### When Using AI
1. **Review generated code** - Always verify AI suggestions
2. **Test thoroughly** - Run tests after AI changes
3. **Check style** - Ensure code follows PSR-12
4. **Understand changes** - Don't blindly accept suggestions

### Context Management
- AI has access to your entire codebase
- Reference specific files for better context
- Use `@filename` to mention specific files in chat

### Laravel-Specific Tips
- AI knows Laravel conventions
- Ask for Eloquent relationships
- Request Artisan command generation
- Generate migrations with AI help

## Debugging

### Xdebug Configuration
1. Install Xdebug extension for PHP
2. Configure `.vscode/launch.json` (already provided)
3. Set breakpoints in your code
4. Press `F5` to start debugging

### PHP Debug Settings
```json
{
    "php.debug.executable": "/usr/bin/php",
    "php.validate.enable": true,
    "php.validate.run": "onType"
}
```

## Workspace Settings

The project includes `.cursor/settings.json` with:
- PHP 8.2 configuration
- Laravel 11.x settings
- Strict types enabled
- PSR-12 coding standards
- Auto-format on save

## Troubleshooting

### AI Not Working
1. Check API key configuration
2. Verify internet connection
3. Restart Cursor

### Intellisense Issues
1. Reload window (`Cmd+Shift+P` > "Developer: Reload Window")
2. Clear cache: `Cmd+Shift+P` > "PHP: Restart Language Server"

### Formatting Issues
1. Ensure Pint or PHP-CS-Fixer is installed
2. Run `make lint` to check style
3. Run `make cs-fix` to auto-fix

## Additional Resources

- [Cursor Documentation](https://docs.cursor.com)
- [Laravel Documentation](https://laravel.com/docs)
- [PHP The Right Way](https://phptherightway.com)
- [PSR-12 Coding Style](https://www.php-fig.org/psr/psr-12/)
