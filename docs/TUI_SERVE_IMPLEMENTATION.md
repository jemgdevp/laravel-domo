# 🏠 Laravel Domo - TUI & Serve Implementation

## Web Dashboard (`domo:serve`)

### Architecture

```
┌─────────────────────────────────────┐
│   DomoServeCommand                  │
│   - Starts development server       │
│   - Configures host/port            │
│   - Opens browser (optional)        │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   DashboardServer                   │
│   - Registers routes                │
│   - Manages server lifecycle        │
│   - Provides URL helpers            │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   DashboardController               │
│   - index() - Dashboard home        │
│   - schema() - Schema viewer        │
│   - models() - Models viewer        │
│   - analyze() - AI analysis         │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   Blade Views                       │
│   - layout.blade.php                │
│   - index.blade.php                 │
│   - schema.blade.php                │
│   - models.blade.php                │
└─────────────────────────────────────┘
```

### Usage

```bash
# Start server with defaults (127.0.0.1:8080)
php artisan domo:serve

# Custom host and port
php artisan domo:serve --host=0.0.0.0 --port=3000

# Open in browser automatically
php artisan domo:serve --open

# Full example
php artisan domo:serve --host=127.0.0.1 --port=8080 --open
```

### Configuration

```env
DOMO_DASHBOARD_ENABLED=true
DOMO_DASHBOARD_ROUTE=domo
DOMO_DASHBOARD_HOST=127.0.0.1
DOMO_DASHBOARD_PORT=8080
```

### Features

- ✅ Responsive design with modern CSS
- ✅ Database schema visualization
- ✅ Eloquent models viewer
- ✅ AI analysis integration (coming)
- ✅ Migration management (coming)
- ✅ Export functionality (coming)

---

## Terminal UI (`domo:tui`)

### Architecture

```
┌─────────────────────────────────────┐
│   DomoTuiCommand                    │
│   - Initializes TUI                 │
│   - Configures theme/colors         │
│   - Starts screen manager           │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   ScreenManager                     │
│   - Main menu loop                  │
│   - Screen navigation               │
│   - Event handling                  │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   TuiService                        │
│   - Laravel Prompts wrapper         │
│   - Menu rendering                  │
│   - Input handling                  │
│   - Success/Error messages          │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│   Laravel Prompts                   │
│   - menu()                          │
│   - select()                        │
│   - text()                          │
│   - confirm()                       │
│   - spinner()                       │
└─────────────────────────────────────┘
```

### Usage

```bash
# Start TUI with defaults
php artisan domo:tui

# Disable colors
php artisan domo:tui --no-colors

# Use simple theme
php artisan domo:tui --simple
```

### Configuration

```env
DOMO_TUI_ENABLED=true
DOMO_TUI_THEME=default
DOMO_TUI_COLORS=true
```

### Screens

1. **Main Menu**
   ```
   Laravel Domo - Main Menu
   ├─ 📊 View Database Schema
   ├─ 🔧 View Eloquent Models
   ├─ 🤖 AI Analysis
   ├─ 📝 Manage Migrations
   ├─ 📤 Export SQL
   └─ ❌ Exit
   ```

2. **Schema Screen**
   - Table selection menu
   - Column details display
   - Index information
   - Foreign keys

3. **Models Screen**
   - List of Eloquent models
   - Relationships display
   - AI suggestions

4. **AI Analysis Screen**
   - Loading spinner
   - Analysis results
   - Recommendations

5. **Migrations Screen** (Coming Soon)
   - Pending migrations
   - Generate new migration
   - Preview changes

6. **Export Screen** (Coming Soon)
   - Export SQL dump
   - Export migrations
   - Import functionality

### Dependencies

- `laravel/prompts` - Terminal prompts
- `nunomaduro/termwind` - Terminal styling

---

## Implementation Status

### Dashboard (Serve)
- [x] Command structure
- [x] Server service
- [x] Configuration
- [x] Routes
- [x] Controllers
- [x] Blade views
- [ ] Development server integration
- [ ] Live reload
- [ ] WebSocket support

### TUI
- [x] Command structure
- [x] TUI service
- [x] Screen manager
- [x] Main menu
- [x] Schema screen (basic)
- [x] Models screen (basic)
- [ ] Full schema display
- [ ] AI analysis integration
- [ ] Migration management
- [ ] Export/Import

### Next Steps

1. **Dashboard**
   - Implement PHP built-in server wrapper
   - Add live reload
   - Implement AI analysis endpoint
   - Add migration preview

2. **TUI**
   - Enhance table display with Termwind
   - Add keyboard navigation
   - Implement search functionality
   - Add export options

---

## Code Examples

### Custom TUI Component

```php
use Jemgdevp\Domo\Services\TUI\TuiService;

$tui = app(TuiService::class);

// Show menu
$choice = $tui->renderMainMenu();

// Get input
$name = $tui->text('Enter table name:');

// Confirm action
if ($tui->confirm('Are you sure?')) {
    // Do something
}

// Show loading
$tui->withSpinner('Processing...', function () {
    // Long running task
});

// Success/Error
$tui->success('Operation completed!');
$tui->error('Something went wrong');
```

### Dashboard Route

```php
use Illuminate\Support\Facades\Route;
use Jemgdevp\Domo\Http\Controllers\DashboardController;

Route::prefix('domo')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/schema/{table}', [DashboardController::class, 'schema']);
    Route::post('/analyze', [DashboardController::class, 'analyze']);
});
```
