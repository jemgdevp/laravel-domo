# 🎨 Laravel Domo - UI Architecture

## Web Dashboard Architecture

### Design Philosophy
- **Modern & Clean**: Minimalist design with focus on content
- **Responsive**: Works on desktop, tablet, and mobile
- **Fast**: Optimized loading with lazy loading and caching
- **Accessible**: WCAG 2.1 AA compliant
- **Laravel-native**: Uses Blade, Tailwind CSS, and Alpine.js

### Tech Stack

```
Frontend Stack:
├── Blade Templates (Server-side rendering)
├── Tailwind CSS 3.x (Styling)
├── Alpine.js 3.x (Interactivity)
├── Laravel Vite (Asset bundling)
└── Laravel Prompts colors (Terminal-inspired palette)

Backend Stack:
├── Laravel 11-13
├── Laravel Prompts (Shared logic)
├── Termwind (Terminal-style components)
└── Livewire 3.x (Optional real-time)
```

### Color Palette

```css
/* Laravel-inspired colors */
--color-laravel-red: #FF2D20;
--color-laravel-dark: #1F2937;
--color-laravel-gray: #4B5563;
--color-laravel-light: #F9FAFB;

/* Terminal-inspired colors */
--color-success: #10B981;    /* Green */
--color-warning: #F59E0B;    /* Amber */
--color-error: #EF4444;      /* Red */
--color-info: #3B82F6;       /* Blue */
--color-primary: #FF2D20;    /* Laravel Red */
```

### Layout Structure

```
┌─────────────────────────────────────────────────────────┐
│  Header (Fixed)                                         │
│  ┌──────────────────────────────────────────────────┐  │
│  │ 🏠 Domo    Schema    Models    AI    Export     │  │
│  └──────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────┤
│  Main Content                                           │
│  ┌─────────────────────────────────────────────────┐   │
│  │                                                  │   │
│  │  Dashboard Home                                  │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐        │   │
│  │  │ Tables   │ │ Models   │ │ Database │        │   │
│  │  │   42     │ │   15     │ │  MySQL   │        │   │
│  │  └──────────┘ └──────────┘ └──────────┘        │   │
│  │                                                  │   │
│  │  Recent Activity                                 │   │
│  │  ┌─────────────────────────────────────────┐    │   │
│  │  │ • Schema analyzed (2 min ago)           │    │   │
│  │  │ • Migration generated (5 min ago)       │    │   │
│  │  └─────────────────────────────────────────┘    │   │
│  │                                                  │   │
│  └─────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────┤
│  Footer                                                 │
│  Laravel Domo v0.1.0 | Made with ❤️ for Laravel        │
└─────────────────────────────────────────────────────────┘
```

### Component Library

```
Components/
├── Layout/
│   ├── Header.blade.php
│   ├── Footer.blade.php
│   ├── Sidebar.blade.php
│   └── Navigation.blade.php
│
├── Dashboard/
│   ├── StatsCard.blade.php      # Statistics cards
│   ├── ActivityFeed.blade.php   # Recent activity
│   ├── QuickActions.blade.php   # Action buttons
│   └── DatabaseInfo.blade.php   # Database details
│
├── Schema/
│   ├── TableList.blade.php      # Tables grid
│   ├── TableDetail.blade.php    # Single table view
│   ├── ColumnTable.blade.php    # Columns display
│   └── IndexList.blade.php      # Indexes display
│
├── Models/
│   ├── ModelCard.blade.php      # Model overview
│   ├── RelationshipGraph.blade.php  # Visual relationships
│   └── ModelDetail.blade.php    # Full model details
│
├── AI/
│   ├── AnalysisPanel.blade.php  # AI results
│   ├── SuggestionCard.blade.php # AI suggestions
│   └── ChatInterface.blade.php  # AI chat (future)
│
└── UI/
    ├── Button.blade.php
    ├── Card.blade.php
    ├── Badge.blade.php
    ├── Alert.blade.php
    ├── Modal.blade.php
    ├── Dropdown.blade.php
    └── LoadingSpinner.blade.php
```

### Screens

#### 1. Dashboard Home
```
/dashboard
├── Stats Overview
│   ├── Total Tables
│   ├── Total Models
│   ├── Database Size
│   └── Last Analysis
│
├── Quick Actions
│   ├── Analyze Schema
│   ├── Generate Migration
│   ├── Export SQL
│   └── Run AI Analysis
│
└── Recent Activity
    ├── Schema changes
    ├── Migrations created
    └── AI analyses
```

#### 2. Schema Viewer
```
/schema
├── Table Grid View
│   ├── Search/Filter
│   ├── Sort by name/size
│   └── Quick actions per table
│
├── Table Detail View
│   ├── Columns
│   │   ├── Name
│   │   ├── Type
│   │   ├── Nullable
│   │   ├── Default
│   │   └── Indexes
│   │
│   ├── Indexes
│   │   ├── Primary
│   │   ├── Unique
│   │   └── Regular
│   │
│   └── Foreign Keys
│       ├── References
│       └── Constraints
│
└── Actions
    ├── Export Table SQL
    ├── Copy Structure
    └── AI Analysis
```

#### 3. Models Viewer
```
/models
├── Model List
│   ├── Search/Filter
│   ├── Group by namespace
│   └── Status indicators
│
├── Model Detail
│   ├── Properties
│   ├── Relationships
│   │   ├── HasMany
│   │   ├── HasOne
│   │   ├── BelongsTo
│   │   └── BelongsToMany
│   │
│   ├── Scopes
│   ├── Accessors/Mutators
│   └── Events
│
└── AI Suggestions
    ├── Missing relationships
    ├── Optimization tips
    └── Best practices
```

#### 4. AI Analysis
```
/ai/analyze
├── Analysis Type Selection
│   ├── Schema Analysis
│   ├── Model Analysis
│   └── Migration Suggestions
│
├── Results Display
│   ├── Issues Found
│   ├── Suggestions
│   ├── Code Examples
│   └── Apply Button
│
└── History
    └── Previous analyses
```

---

## Terminal UI (TUI) Architecture

### Design Philosophy
- **Fast**: Instant response, no lag
- **Intuitive**: Keyboard-first navigation
- **Beautiful**: Colors, icons, smooth animations
- **Efficient**: Minimal keystrokes for common actions

### Tech Stack

```
TUI Stack:
├── Laravel Prompts (Core prompts)
├── Termwind (Terminal styling)
├── PHP CLI (Runtime)
└── ANSI Colors (Visual enhancement)
```

### Keyboard Navigation

```
Global Shortcuts:
├── q           - Quit
├── ?           - Help
├── /           - Search
├── r           - Refresh
├── h           - Go home
├── ←/→         - Navigate sections
├── ↑/↓         - Navigate items
├── Enter       - Select/Confirm
├── Esc         - Back/Cancel
└── Ctrl+R      - Reload
```

### Screen Flow

```
┌─────────────────────────────────────────┐
│         MAIN MENU                       │
│  ┌───────────────────────────────────┐ │
│  │  🏠 Laravel Domo TUI              │ │
│  │                                   │ │
│  │  > 📊 Database Schema            │ │
│  │    🔧 Eloquent Models            │ │
│  │    🤖 AI Analysis                │ │
│  │    📝 Migrations                 │ │
│  │    📤 Export/Import              │ │
│  │    ⚙️  Settings                  │ │
│  │    ❌ Exit                       │ │
│  │                                   │ │
│  │  ↑↓ Navigate  Enter Select       │ │
│  └───────────────────────────────────┘ │
└─────────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────┐
│      SCHEMA SCREEN                      │
│  ┌───────────────────────────────────┐ │
│  │  📊 Database Schema               │ │
│  │                                   │ │
│  │  Tables:                          │ │
│  │  > users (12 columns)            │ │
│  │    posts (8 columns)             │ │
│  │    comments (6 columns)          │ │
│  │    tags (4 columns)              │ │
│  │                                   │ │
│  │  [Enter] View  [D] Delete        │ │
│  │  [E] Export  [B] Back            │ │
│  └───────────────────────────────────┘ │
└─────────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────┐
│    TABLE DETAIL SCREEN                  │
│  ┌───────────────────────────────────┐ │
│  │  📋 users                         │ │
│  │                                   │ │
│  │  Columns:                         │ │
│  │  ┌──────┬─────────┬──────┬─────┐ │ │
│  │  │ Name │ Type    │ Null │ Key │ │ │
│  │  ├──────┼─────────┼──────┼─────┤ │ │
│  │  │ id   │ BIGINT  │ No   │ PRI │ │ │
│  │  │ name │ VARCHAR │ Yes  │     │ │ │
│  │  │ email│ VARCHAR │ No   │ UNI │ │ │
│  │  └──────┴─────────┴──────┴─────┘ │ │
│  │                                   │ │
│  │  Indexes: 2  Foreign Keys: 0      │ │
│  │                                   │ │
│  │  [Tab] Switch  [B] Back          │ │
│  └───────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

### TUI Components

```
Components/
├── MainMenu.php           # Main navigation
├── TableList.php          # Tables display
├── TableDetail.php        # Single table view
├── ModelList.php          # Models display
├── ModelDetail.php        # Single model view
├── AIAnalysis.php         # AI interface
├── MigrationManager.php   # Migrations UI
├── ExportImport.php       # Export/Import UI
└── Settings.php           # Settings UI

Helpers/
├── KeyboardHandler.php    # Keyboard input
├── ColorTheme.php         # Color schemes
├── IconSet.php            # Unicode icons
├── LayoutRenderer.php     # Layout engine
└── AnimationHelper.php    # Smooth transitions
```

### Color Themes

```php
// Laravel Theme (default)
'laravel' => [
    'primary' => 'red',
    'secondary' => 'gray',
    'success' => 'green',
    'warning' => 'yellow',
    'error' => 'red',
    'info' => 'blue',
],

// Monokai Theme
'monokai' => [
    'primary' => 'magenta',
    'secondary' => 'white',
    'success' => 'green',
    'warning' => 'yellow',
    'error' => 'red',
    'info' => 'cyan',
],

// Nord Theme
'nord' => [
    'primary' => 'blue',
    'secondary' => 'white',
    'success' => 'green',
    'warning' => 'yellow',
    'error' => 'red',
    'info' => 'cyan',
],
```

### Animations

```php
// Spinner for loading
⠋ Loading...
⠙
⠹
⠸
⠼
⠴
⠦
⠧
⠇
⠏

// Progress bar
[████████████░░░░░░░░] 60%

// Success check
✓ Operation completed

// Error cross
✗ Operation failed
```

---

## Performance Optimization

### Web Dashboard

1. **Lazy Loading**: Load views on demand
2. **Caching**: Cache schema analysis results
3. **Debouncing**: Debounce search inputs
4. **Virtual Scrolling**: For large table lists
5. **Service Worker**: Offline support (future)

### TUI

1. **Minimal Redraws**: Only update changed areas
2. **Async Operations**: Non-blocking I/O
3. **Keyboard Buffering**: Smooth input handling
4. **Color Caching**: Pre-compute ANSI codes
5. **Memory Efficient**: Stream large datasets

---

## Accessibility

### Web Dashboard
- [ ] Semantic HTML
- [ ] ARIA labels
- [ ] Keyboard navigation
- [ ] High contrast mode
- [ ] Screen reader support
- [ ] Focus indicators

### TUI
- [ ] High contrast colors
- [ ] Large text option
- [ ] Keyboard-only navigation
- [ ] Clear error messages
- [ ] Help system

---

## Future Enhancements

### Web Dashboard
- [ ] Dark mode toggle
- [ ] Customizable dashboard widgets
- [ ] Real-time updates (WebSockets)
- [ ] Export to PDF
- [ ] Shareable schema diagrams
- [ ] Collaboration features

### TUI
- [ ] Mouse support
- [ ] Fuzzy search
- [ ] Command palette (Ctrl+K)
- [ ] Plugin system
- [ ] Custom themes
- [ ] Session persistence
