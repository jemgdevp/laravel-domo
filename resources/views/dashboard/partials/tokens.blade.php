<style>
    /* ============================================================
       LARAVEL DOMO — DESIGN SYSTEM TOKENS
       Dark is default on :root. Light overrides under [data-theme="light"].
       ============================================================ */
    :root,
    [data-theme="dark"] {
        /* Backgrounds */
        --bg-primary: #0a0a0a;
        --bg-secondary: #111111;
        --bg-tertiary: #1a1a1a;
        --bg-elevated: #1e1e1e;

        /* Text */
        --text-primary: #ffffff;
        --text-secondary: #a0a0a0;
        --text-muted: #909090; /* AA: >=4.5:1 on the darkest surfaces (#0a0a0a/#111/#1a1a1a) */

        /* Borders */
        --border-color: #333333;
        --border-subtle: #242424;
        --border-strong: #444444;

        /* Accent (Laravel red) */
        --accent: #ff2d20;
        --accent-hover: #ff4736;
        --accent-active: #e0241a;
        /* Solid accent fill for text-bearing controls (buttons): #ffffff on this reaches AA */
        --accent-strong: #e0241a;
        --accent-soft: rgba(255, 45, 32, 0.12);
        --accent-soft-hover: rgba(255, 45, 32, 0.18);
        --accent-glow: rgba(255, 45, 32, 0.3);
        --on-accent: #ffffff;

        /* Semantic */
        --success: #22c55e;
        --success-soft: rgba(34, 197, 94, 0.14);
        --warning: #f59e0b;
        --warning-soft: rgba(245, 158, 11, 0.14);
        --error: #ef4444;
        --error-soft: rgba(239, 68, 68, 0.14);
        --info: #3b82f6;
        --info-soft: rgba(59, 130, 246, 0.14);

        /* Badge text tokens — AA (>=4.5:1) when composited over their *-soft tints */
        --badge-primary-text: #ff5347;
        --badge-warning-text: #f59e0b;
        --badge-info-text: #5b9bff;

        /* Surfaces for interactive states */
        --hover-overlay: rgba(255, 255, 255, 0.04);
        --active-overlay: rgba(255, 255, 255, 0.07);
        --backdrop: rgba(0, 0, 0, 0.7);
        --scrim: rgba(10, 10, 10, 0.72);
        --focus-ring: rgba(255, 45, 32, 0.55);
        --skeleton-base: #1a1a1a;
        --skeleton-shine: rgba(255, 255, 255, 0.06);

        /* Grid / glow opacities used by ambient background */
        --grid-line: rgba(255, 255, 255, 0.03);
        --glow-strength: 0.08;
        --scanline-opacity: 0;
    }

    [data-theme="light"] {
        /* Backgrounds */
        --bg-primary: #ffffff;
        --bg-secondary: #f6f6f7;
        --bg-tertiary: #eeeef1;
        --bg-elevated: #ffffff;

        /* Text */
        --text-primary: #16181d;
        --text-secondary: #565a63;
        --text-muted: #65696f; /* AA: >=4.5:1 incl. on bg-tertiary #eeeef1 (palette-trigger placeholder) */

        /* Borders */
        --border-color: #e2e2e6;
        --border-subtle: #ededf0;
        --border-strong: #cfcfd5;

        /* Accent (Laravel red) — darkened slightly for AA on light bg */
        --accent: #e0241a;
        --accent-hover: #c41f16;
        --accent-active: #a81a12;
        /* Button fill already AA on light: #ffffff on #e0241a = 4.73:1 */
        --accent-strong: #e0241a;
        --accent-soft: rgba(224, 36, 26, 0.10);
        --accent-soft-hover: rgba(224, 36, 26, 0.16);
        --accent-glow: rgba(255, 45, 32, 0.28);
        --on-accent: #ffffff;

        /* Semantic — AA legible on light */
        --success: #1a7f43;
        --success-soft: rgba(26, 127, 67, 0.12);
        --warning: #a86200;
        --warning-soft: rgba(168, 98, 0, 0.12);
        --error: #c8281f;
        --error-soft: rgba(200, 40, 31, 0.12);
        --info: #1d4ed8;
        --info-soft: rgba(29, 78, 216, 0.12);

        /* Badge text tokens — AA (>=4.5:1) over their *-soft tints on light surfaces */
        --badge-primary-text: #b01b10;
        --badge-warning-text: #8a5200;
        --badge-info-text: #1d4ed8;

        /* Surfaces */
        --hover-overlay: rgba(0, 0, 0, 0.035);
        --active-overlay: rgba(0, 0, 0, 0.06);
        --backdrop: rgba(20, 20, 25, 0.4);
        --scrim: rgba(255, 255, 255, 0.78);
        --focus-ring: rgba(224, 36, 26, 0.5);
        --skeleton-base: #ececef;
        --skeleton-shine: rgba(0, 0, 0, 0.05);

        --grid-line: rgba(0, 0, 0, 0.04);
        --glow-strength: 0.05;
        --scanline-opacity: 0;
    }

    :root {
        /* Typography */
        --font-sans: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-mono: 'JetBrains Mono', 'Fira Code', 'SFMono-Regular', 'Consolas', ui-monospace, monospace;

        /* Spacing scale */
        --space-0: 0;
        --space-1: 0.25rem;
        --space-2: 0.5rem;
        --space-3: 0.75rem;
        --space-4: 1rem;
        --space-5: 1.5rem;
        --space-6: 2rem;
        --space-8: 3rem;
        --space-10: 4rem;

        /* Radii */
        --radius-sm: 4px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-pill: 999px;

        /* Shadows */
        --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.25);
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.28);
        --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.35);
        --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.5);
        --shadow-glow: 0 6px 24px var(--accent-glow);

        /* Z-index scale */
        --z-bg: 0;
        --z-base: 1;
        --z-sidebar: 40;
        --z-topbar: 50;
        --z-drawer-backdrop: 60;
        --z-drawer: 70;
        --z-palette: 90;
        --z-toast: 100;
        --z-skip: 110;

        /* Layout dimensions */
        --sidebar-w: 248px;
        --sidebar-w-collapsed: 68px;
        --topbar-h: 60px;
        --content-max: 1200px;

        /* Transitions */
        --t-fast: 120ms cubic-bezier(0.4, 0, 0.2, 1);
        --t-base: 200ms cubic-bezier(0.4, 0, 0.2, 1);
        --t-slow: 320ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ============================================================ RESET */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    [x-cloak] { display: none !important; }

    html { font-size: 16px; -webkit-text-size-adjust: 100%; }

    body {
        font-family: var(--font-sans);
        background: var(--bg-primary);
        color: var(--text-primary);
        line-height: 1.6;
        min-height: 100vh;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        transition: background-color var(--t-base), color var(--t-base);
    }

    .mono { font-family: var(--font-mono); font-feature-settings: 'tnum' 1, 'zero' 1; }

    a { color: inherit; }
    button { font-family: inherit; }

    ::selection { background: var(--accent-soft-hover); color: var(--text-primary); }

    /* Custom scrollbars */
    * { scrollbar-width: thin; scrollbar-color: var(--border-strong) transparent; }
    *::-webkit-scrollbar { width: 10px; height: 10px; }
    *::-webkit-scrollbar-track { background: transparent; }
    *::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 999px; border: 3px solid transparent; background-clip: content-box; }
    *::-webkit-scrollbar-thumb:hover { background: var(--border-strong); background-clip: content-box; }

    /* ============================================================ FOCUS */
    :focus { outline: none; }
    :focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
        border-radius: var(--radius-sm);
    }
    .skip-link {
        position: fixed;
        top: var(--space-2);
        left: var(--space-2);
        z-index: var(--z-skip);
        background: var(--accent);
        color: var(--on-accent);
        padding: var(--space-2) var(--space-4);
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transform: translateY(-150%);
        transition: transform var(--t-fast);
    }
    .skip-link:focus-visible { transform: translateY(0); outline-offset: 3px; }

    /* ============================================================ KEYFRAMES */
    @keyframes domo-blink { 0%, 55%, 100% { opacity: 1; } 28% { opacity: 0.25; } }
    @keyframes domo-shimmer { 100% { transform: translateX(100%); } }
    @keyframes domo-spin { to { transform: rotate(360deg); } }
    @keyframes domo-pop { from { opacity: 0; transform: translateY(6px) scale(0.98); } to { opacity: 1; transform: none; } }
    @keyframes domo-toast-in { from { opacity: 0; transform: translateX(16px); } to { opacity: 1; transform: none; } }
    @keyframes domo-scan { 0% { background-position-y: 0; } 100% { background-position-y: 4px; } }

    .animate-fade-in { animation: domo-pop var(--t-slow) both; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }

    /* ============================================================ AMBIENT BACKGROUND */
    .app-bg { position: fixed; inset: 0; z-index: var(--z-bg); pointer-events: none; overflow: hidden; }
    .app-bg-grid {
        position: absolute; inset: 0;
        background-image:
            linear-gradient(var(--grid-line) 1px, transparent 1px),
            linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
        background-size: 50px 50px;
        mask-image: radial-gradient(ellipse 90% 70% at 60% 0%, #000 30%, transparent 100%);
        -webkit-mask-image: radial-gradient(ellipse 90% 70% at 60% 0%, #000 30%, transparent 100%);
    }
    .app-bg-glow {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 50% 40% at 75% 8%, rgba(255, 45, 32, var(--glow-strength)) 0%, transparent 60%),
            radial-gradient(ellipse 40% 50% at 12% 85%, rgba(255, 45, 32, calc(var(--glow-strength) * 0.6)) 0%, transparent 55%);
    }
    .app-bg-scanline {
        position: absolute; inset: 0;
        opacity: var(--scanline-opacity);
        background: repeating-linear-gradient(0deg, rgba(0,0,0,0.5) 0px, rgba(0,0,0,0.5) 1px, transparent 1px, transparent 3px);
    }

    /* ============================================================ APP SHELL */
    .app-frame {
        margin-left: var(--sidebar-w);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: margin-left var(--t-base);
        position: relative;
        z-index: var(--z-base);
    }
    body.sidebar-collapsed .app-frame { margin-left: var(--sidebar-w-collapsed); }

    /* SIDEBAR */
    .sidebar {
        position: fixed;
        top: 0; left: 0; bottom: 0;
        width: var(--sidebar-w);
        z-index: var(--z-sidebar);
        background: var(--bg-secondary);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        transition: width var(--t-base), transform var(--t-base);
    }
    body.sidebar-collapsed .sidebar { width: var(--sidebar-w-collapsed); }

    .sidebar-head {
        height: var(--topbar-h);
        display: flex;
        align-items: center;
        gap: var(--space-3);
        padding: 0 var(--space-4);
        border-bottom: 1px solid var(--border-subtle);
        flex-shrink: 0;
    }
    .sidebar-brand {
        display: flex; align-items: center; gap: var(--space-3);
        text-decoration: none; color: var(--text-primary);
        font-family: var(--font-mono); font-weight: 700; font-size: 0.95rem;
        letter-spacing: 0.04em; white-space: nowrap; overflow: hidden;
    }
    .pixel-dot {
        width: 9px; height: 9px; flex-shrink: 0;
        background: var(--accent);
        box-shadow: 0 0 8px var(--accent-glow);
        animation: domo-blink 2.4s infinite steps(1, end);
    }
    body.sidebar-collapsed .sidebar-brand-text { opacity: 0; width: 0; }
    .sidebar-brand-text { transition: opacity var(--t-fast); }

    .sidebar-nav { padding: var(--space-4) var(--space-3); display: flex; flex-direction: column; gap: var(--space-1); flex: 1; overflow-y: auto; }
    .nav-section-label {
        font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.12em;
        color: var(--text-muted); padding: var(--space-3) var(--space-3) var(--space-2);
        font-family: var(--font-mono);
    }
    body.sidebar-collapsed .nav-section-label { opacity: 0; height: 0; padding: 0; overflow: hidden; }

    .nav-item {
        display: flex; align-items: center; gap: var(--space-3);
        padding: var(--space-2) var(--space-3);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.875rem; font-weight: 500;
        position: relative;
        transition: background var(--t-fast), color var(--t-fast);
        white-space: nowrap;
    }
    .nav-item:hover { background: var(--hover-overlay); color: var(--text-primary); }
    .nav-item .nav-icon { width: 20px; height: 20px; flex-shrink: 0; display: grid; place-items: center; }
    .nav-item .nav-icon svg { width: 18px; height: 18px; stroke: currentColor; }
    .nav-item .nav-label { transition: opacity var(--t-fast); }
    body.sidebar-collapsed .nav-item .nav-label { opacity: 0; width: 0; overflow: hidden; }
    body.sidebar-collapsed .nav-item { justify-content: center; }

    .nav-item.is-active {
        background: var(--accent-soft);
        color: var(--accent);
    }
    .nav-item.is-active::before {
        content: ''; position: absolute; left: -1px; top: 50%; transform: translateY(-50%);
        width: 3px; height: 60%; background: var(--accent); border-radius: 0 999px 999px 0;
        box-shadow: 0 0 10px var(--accent-glow);
    }

    .sidebar-foot { padding: var(--space-3); border-top: 1px solid var(--border-subtle); flex-shrink: 0; }
    .sidebar-collapse-btn {
        width: 100%; display: flex; align-items: center; gap: var(--space-3);
        padding: var(--space-2) var(--space-3); border-radius: var(--radius-md);
        background: transparent; border: 1px solid var(--border-subtle); color: var(--text-secondary);
        cursor: pointer; font-size: 0.8125rem; transition: background var(--t-fast), color var(--t-fast), border-color var(--t-fast);
    }
    .sidebar-collapse-btn:hover { background: var(--hover-overlay); color: var(--text-primary); border-color: var(--border-color); }
    body.sidebar-collapsed .sidebar-collapse-btn { justify-content: center; }
    body.sidebar-collapsed .sidebar-collapse-btn .nav-label { display: none; }
    .sidebar-collapse-btn svg { width: 16px; height: 16px; stroke: currentColor; transition: transform var(--t-base); }
    body.sidebar-collapsed .sidebar-collapse-btn svg { transform: rotate(180deg); }

    /* TOPBAR */
    .topbar {
        position: sticky; top: 0; z-index: var(--z-topbar);
        height: var(--topbar-h);
        display: flex; align-items: center; gap: var(--space-3);
        padding: 0 var(--space-5);
        background: var(--scrim);
        backdrop-filter: blur(14px) saturate(140%);
        -webkit-backdrop-filter: blur(14px) saturate(140%);
        border-bottom: 1px solid var(--border-color);
    }
    .topbar-spacer { flex: 1; }
    .topbar-mobile-logo { display: none; align-items: center; gap: var(--space-2); text-decoration: none; color: var(--text-primary); font-family: var(--font-mono); font-weight: 700; font-size: 0.9rem; }

    .hamburger { display: none; }

    /* Command palette trigger */
    .palette-trigger {
        display: inline-flex; align-items: center; gap: var(--space-3);
        height: 36px; padding: 0 var(--space-3) 0 var(--space-4);
        min-width: 220px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-muted);
        font-size: 0.8125rem; cursor: pointer;
        transition: border-color var(--t-fast), background var(--t-fast), color var(--t-fast);
    }
    .palette-trigger:hover { border-color: var(--border-strong); color: var(--text-secondary); }
    .palette-trigger .pt-icon { width: 15px; height: 15px; stroke: currentColor; flex-shrink: 0; }
    .palette-trigger .pt-label { flex: 1; text-align: left; }
    .kbd {
        font-family: var(--font-mono); font-size: 0.7rem; line-height: 1;
        padding: 3px 6px; border-radius: var(--radius-sm);
        background: var(--bg-elevated); border: 1px solid var(--border-color);
        color: var(--text-secondary); box-shadow: 0 1px 0 var(--border-color);
    }

    /* ============================================================ MAIN */
    .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .main:focus-visible { outline: none; }
    .main-inner {
        width: 100%; max-width: var(--content-max);
        margin: 0 auto; padding: var(--space-8) var(--space-6);
        flex: 1;
    }
    @media (max-width: 640px) { .main-inner { padding: var(--space-5) var(--space-4); } }

    .page-header { margin-bottom: var(--space-6); }
    .page-title { font-size: 1.6rem; font-weight: 700; letter-spacing: -0.02em; line-height: 1.2; }
    .page-subtitle { color: var(--text-secondary); margin-top: var(--space-2); font-size: 0.95rem; }
    .page-eyebrow { font-family: var(--font-mono); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.12em; color: var(--accent); margin-bottom: var(--space-2); }

    /* FOOTER */
    .footer { border-top: 1px solid var(--border-subtle); margin-top: auto; }
    .footer-inner {
        max-width: var(--content-max); margin: 0 auto;
        padding: var(--space-5) var(--space-6);
        display: flex; align-items: center; gap: var(--space-4);
        color: var(--text-muted); font-size: 0.8rem; flex-wrap: wrap;
    }
    .footer-logo { display: inline-flex; align-items: center; gap: var(--space-2); }
    .footer-logo .pixel-dot { width: 7px; height: 7px; }
    .footer-meta { color: var(--text-secondary); }
    .footer-credit { margin-left: auto; }

    /* ============================================================ COMPONENTS — CARD */
    .card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: var(--space-5);
        margin-bottom: var(--space-5);
        transition: border-color var(--t-base);
    }
    .card:hover { border-color: var(--border-strong); }
    .card.is-flush { padding: 0; overflow: hidden; }
    .card-header {
        display: flex; align-items: center; justify-content: space-between; gap: var(--space-3);
        margin-bottom: var(--space-5); padding-bottom: var(--space-4);
        border-bottom: 1px solid var(--border-subtle);
    }
    .card-title { font-size: 1.05rem; font-weight: 600; display: flex; align-items: center; gap: var(--space-2); letter-spacing: -0.01em; }
    .card-title svg { width: 18px; height: 18px; stroke: var(--accent); }
    .card-actions { display: flex; align-items: center; gap: var(--space-2); }

    /* STAT CARD */
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: var(--space-4); margin-bottom: var(--space-5);
    }
    .stat-card {
        background: var(--bg-secondary); border: 1px solid var(--border-color);
        border-radius: var(--radius-lg); padding: var(--space-5);
        position: relative; overflow: hidden;
        transition: transform var(--t-base), border-color var(--t-base);
    }
    .stat-card::after {
        content: ''; position: absolute; inset: 0; pointer-events: none;
        background: radial-gradient(circle at 100% 0%, var(--accent-soft) 0%, transparent 45%);
        opacity: 0; transition: opacity var(--t-base);
    }
    .stat-card:hover { transform: translateY(-3px); border-color: var(--border-strong); }
    .stat-card:hover::after { opacity: 1; }
    .stat-label { font-size: 0.78rem; color: var(--text-secondary); font-weight: 500; display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-3); }
    .stat-label svg { width: 15px; height: 15px; stroke: var(--text-muted); }
    .stat-value { font-family: var(--font-mono); font-size: 2rem; font-weight: 700; line-height: 1; letter-spacing: -0.02em; }
    .stat-value.is-sm { font-size: 1.25rem; }
    .stat-delta { font-size: 0.75rem; margin-top: var(--space-3); display: inline-flex; align-items: center; gap: var(--space-1); font-family: var(--font-mono); }
    .stat-delta.is-up { color: var(--success); }
    .stat-delta.is-down { color: var(--error); }
    .stat-delta.is-flat { color: var(--text-muted); }

    /* BUTTONS */
    .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: var(--space-2);
        padding: 0 var(--space-4); height: 38px;
        border-radius: var(--radius-md);
        font-size: 0.875rem; font-weight: 600; line-height: 1;
        text-decoration: none; cursor: pointer;
        border: 1px solid transparent;
        white-space: nowrap; user-select: none;
        transition: background var(--t-fast), border-color var(--t-fast), color var(--t-fast), transform var(--t-fast), box-shadow var(--t-fast);
    }
    .btn svg { width: 16px; height: 16px; stroke: currentColor; flex-shrink: 0; }
    .btn:disabled, .btn[aria-disabled="true"] { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
    .btn:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

    .btn-primary { background: var(--accent-strong); color: var(--on-accent); box-shadow: 0 0 0 0 var(--accent-glow); }
    .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); box-shadow: var(--shadow-glow); }
    .btn-primary:active { background: var(--accent-active); transform: translateY(0); }

    .btn-secondary { background: var(--bg-tertiary); color: var(--text-primary); border-color: var(--border-color); }
    .btn-secondary:hover { background: var(--bg-elevated); border-color: var(--border-strong); transform: translateY(-1px); }
    .btn-secondary:active { transform: translateY(0); }

    .btn-ghost { background: transparent; color: var(--text-secondary); }
    .btn-ghost:hover { background: var(--hover-overlay); color: var(--text-primary); }

    .btn-danger { background: var(--error); color: var(--on-accent); }
    .btn-danger:hover { filter: brightness(1.08); transform: translateY(-1px); }

    .btn-sm { height: 30px; padding: 0 var(--space-3); font-size: 0.78rem; }
    .btn-sm svg { width: 14px; height: 14px; }
    .btn-lg { height: 46px; padding: 0 var(--space-5); font-size: 0.95rem; }

    .btn-icon {
        width: 38px; height: 38px; padding: 0; flex-shrink: 0;
        background: transparent; color: var(--text-secondary); border-color: transparent;
    }
    .btn-icon:hover { background: var(--hover-overlay); color: var(--text-primary); }
    .btn-icon.is-bordered { border-color: var(--border-color); background: var(--bg-tertiary); }
    .btn-icon.is-bordered:hover { border-color: var(--border-strong); }
    .btn-icon svg { width: 18px; height: 18px; }
    .btn-icon.btn-sm { width: 30px; height: 30px; }

    /* FORM CONTROLS */
    .field { display: flex; flex-direction: column; gap: var(--space-2); margin-bottom: var(--space-4); }
    .field-label { font-size: 0.8125rem; font-weight: 600; color: var(--text-primary); }
    .field-label .req { color: var(--accent); margin-left: 2px; }
    .field-hint { font-size: 0.75rem; color: var(--text-muted); }
    .field-error { font-size: 0.75rem; color: var(--error); display: flex; align-items: center; gap: var(--space-1); }

    .input, .select, .textarea {
        width: 100%;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        font-family: var(--font-sans); font-size: 0.875rem;
        padding: 0 var(--space-4); height: 38px;
        transition: border-color var(--t-fast), box-shadow var(--t-fast), background var(--t-fast);
    }
    .input::placeholder, .textarea::placeholder { color: var(--text-muted); }
    .input:hover, .select:hover, .textarea:hover { border-color: var(--border-strong); }
    .input:focus, .select:focus, .textarea:focus {
        outline: none; border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-soft);
        background: var(--bg-secondary);
    }
    .input.is-mono, .textarea.is-mono { font-family: var(--font-mono); }
    .textarea { height: auto; min-height: 96px; padding: var(--space-3) var(--space-4); line-height: 1.6; resize: vertical; }
    .select {
        appearance: none; -webkit-appearance: none;
        padding-right: var(--space-8);
        /* Dark theme chevron — matches dark --text-secondary (#a0a0a0), >=3:1 on the field */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23a0a0a0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right var(--space-4) center;
        cursor: pointer;
    }
    /* Light theme chevron — matches light --text-secondary (#565a63) so the indicator adapts to theme */
    [data-theme="light"] .select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23565a63' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    }
    .field-row { display: flex; gap: var(--space-3); flex-wrap: wrap; }
    .field-row > .field { flex: 1; min-width: 180px; margin-bottom: 0; }

    /* Checkbox / switch helpers */
    .checkbox { display: inline-flex; align-items: center; gap: var(--space-2); cursor: pointer; font-size: 0.875rem; }
    .checkbox input { width: 16px; height: 16px; accent-color: var(--accent); cursor: pointer; }

    /* SEARCH INPUT */
    .search { position: relative; display: flex; align-items: center; }
    .search .search-icon { position: absolute; left: var(--space-3); width: 16px; height: 16px; stroke: var(--text-muted); pointer-events: none; }
    .search .input { padding-left: calc(var(--space-3) + 24px); padding-right: calc(var(--space-3) + 28px); }
    .search .search-clear {
        position: absolute; right: var(--space-2); width: 24px; height: 24px;
        display: grid; place-items: center; border: none; background: transparent;
        color: var(--text-muted); cursor: pointer; border-radius: var(--radius-sm);
    }
    .search .search-clear:hover { color: var(--text-primary); background: var(--hover-overlay); }
    .search .search-clear svg { width: 14px; height: 14px; stroke: currentColor; }

    /* TABLE */
    .table-wrap {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: auto;
        max-height: var(--table-max-h, none);
        background: var(--bg-secondary);
    }
    .table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.85rem; }
    .table thead th {
        position: sticky; top: 0; z-index: 2;
        background: var(--bg-tertiary);
        text-align: left; font-weight: 600; color: var(--text-secondary);
        font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.06em;
        padding: var(--space-3) var(--space-4);
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }
    .table tbody td { padding: var(--space-3) var(--space-4); border-bottom: 1px solid var(--border-subtle); vertical-align: middle; }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr { transition: background var(--t-fast); }
    .table tbody tr:hover { background: var(--hover-overlay); }
    .table.is-zebra tbody tr:nth-child(even) { background: rgba(127, 127, 127, 0.04); }
    .table.is-zebra tbody tr:nth-child(even):hover { background: var(--hover-overlay); }
    .table .cell-mono, .table td.cell-mono { font-family: var(--font-mono); font-size: 0.8rem; }
    .table .cell-muted { color: var(--text-muted); }
    .table .cell-num { font-family: var(--font-mono); text-align: right; font-variant-numeric: tabular-nums; }

    /* BADGE */
    .badge {
        display: inline-flex; align-items: center; gap: var(--space-2);
        padding: 3px var(--space-3); height: 22px;
        border-radius: var(--radius-pill);
        font-size: 0.72rem; font-weight: 600; line-height: 1;
        font-family: var(--font-mono); letter-spacing: 0.01em;
        border: 1px solid transparent; white-space: nowrap;
    }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
    .badge.has-pulse .dot { animation: domo-blink 2s infinite steps(1, end); }
    .badge-primary { background: var(--accent-soft); color: var(--badge-primary-text); border-color: var(--accent-soft-hover); }
    .badge-success { background: var(--success-soft); color: var(--success); }
    .badge-warning { background: var(--warning-soft); color: var(--badge-warning-text); }
    .badge-error   { background: var(--error-soft); color: var(--error); }
    .badge-info    { background: var(--info-soft); color: var(--badge-info-text); }
    .badge-muted   { background: var(--bg-tertiary); color: var(--text-secondary); border-color: var(--border-color); }

    /* CHIP */
    .chip {
        display: inline-flex; align-items: center; gap: var(--space-2);
        padding: var(--space-1) var(--space-3); height: 28px;
        background: var(--bg-tertiary); border: 1px solid var(--border-color);
        border-radius: var(--radius-pill); font-size: 0.78rem; color: var(--text-secondary);
        font-family: var(--font-mono); transition: border-color var(--t-fast), color var(--t-fast);
    }
    a.chip:hover, button.chip:hover { border-color: var(--accent); color: var(--text-primary); cursor: pointer; }
    .chip .chip-x { display: grid; place-items: center; width: 14px; height: 14px; border: none; background: transparent; color: var(--text-muted); cursor: pointer; border-radius: 50%; }
    .chip .chip-x:hover { color: var(--text-primary); }
    .chip .chip-x svg { width: 11px; height: 11px; stroke: currentColor; }
    .chip.is-accent { color: var(--accent); border-color: var(--accent-soft-hover); background: var(--accent-soft); }

    /* CODE */
    code, .code {
        font-family: var(--font-mono); font-size: 0.82em;
        background: var(--bg-tertiary); color: var(--text-primary);
        padding: 0.15em 0.45em; border-radius: var(--radius-sm);
        border: 1px solid var(--border-subtle);
    }
    .code-block {
        font-family: var(--font-mono); font-size: 0.8125rem; line-height: 1.6;
        background: var(--bg-primary); border: 1px solid var(--border-color);
        border-radius: var(--radius-md); padding: var(--space-4);
        overflow-x: auto; color: var(--text-secondary);
        white-space: pre;
    }
    .code-block code { background: transparent; border: none; padding: 0; color: inherit; font-size: inherit; }

    /* EMPTY STATE */
    .empty-state {
        display: flex; flex-direction: column; align-items: center; text-align: center;
        padding: var(--space-10) var(--space-5); gap: var(--space-3);
    }
    .empty-state .empty-icon {
        width: 56px; height: 56px; border-radius: var(--radius-lg);
        display: grid; place-items: center;
        background: var(--bg-tertiary); border: 1px solid var(--border-color);
        color: var(--text-muted); margin-bottom: var(--space-2);
    }
    .empty-state .empty-icon svg { width: 26px; height: 26px; stroke: currentColor; }
    .empty-state .empty-title { font-size: 1.05rem; font-weight: 600; color: var(--text-primary); }
    .empty-state .empty-hint { font-size: 0.875rem; color: var(--text-secondary); max-width: 420px; }

    /* SKELETON */
    .skeleton {
        position: relative; overflow: hidden;
        background: var(--skeleton-base); border-radius: var(--radius-sm);
        height: 1em;
    }
    .skeleton::after {
        content: ''; position: absolute; inset: 0; transform: translateX(-100%);
        background: linear-gradient(90deg, transparent, var(--skeleton-shine), transparent);
        animation: domo-shimmer 1.4s infinite;
    }
    .skeleton.is-text { height: 0.8em; margin: 0.35em 0; }
    .skeleton.is-title { height: 1.4em; width: 40%; }
    .skeleton.is-block { height: 120px; border-radius: var(--radius-md); }
    .skeleton.is-circle { border-radius: 50%; aspect-ratio: 1; height: auto; width: 40px; }

    /* SPINNER */
    .spinner {
        display: inline-block; width: 16px; height: 16px;
        border: 2px solid var(--border-strong); border-top-color: var(--accent);
        border-radius: 50%; animation: domo-spin 0.7s linear infinite;
        vertical-align: -2px;
    }

    /* DIVIDER */
    .divider { height: 1px; background: var(--border-subtle); border: none; margin: var(--space-5) 0; }

    /* ============================================================ UTILITIES */
    .flex { display: flex; }
    .inline-flex { display: inline-flex; }
    .grid { display: grid; }
    .items-center { align-items: center; }
    .items-start { align-items: flex-start; }
    .justify-between { justify-content: space-between; }
    .justify-center { justify-content: center; }
    .justify-end { justify-content: flex-end; }
    .flex-wrap { flex-wrap: wrap; }
    .flex-col { flex-direction: column; }
    .flex-1 { flex: 1; }
    .gap-1 { gap: var(--space-1); }
    .gap-2 { gap: var(--space-2); }
    .gap-3 { gap: var(--space-3); }
    .gap-4 { gap: var(--space-4); }
    .gap-5 { gap: var(--space-5); }
    .mt-2 { margin-top: var(--space-2); } .mt-3 { margin-top: var(--space-3); } .mt-4 { margin-top: var(--space-4); } .mt-5 { margin-top: var(--space-5); }
    .mb-2 { margin-bottom: var(--space-2); } .mb-3 { margin-bottom: var(--space-3); } .mb-4 { margin-bottom: var(--space-4); } .mb-5 { margin-bottom: var(--space-5); } .mb-6 { margin-bottom: var(--space-6); }
    .w-full { width: 100%; }
    .text-xs { font-size: 0.75rem; } .text-sm { font-size: 0.875rem; } .text-lg { font-size: 1.125rem; } .text-xl { font-size: 1.4rem; }
    .text-2xl { font-size: 1.75rem; }
    .font-bold { font-weight: 700; } .font-semibold { font-weight: 600; } .font-medium { font-weight: 500; }
    .text-muted { color: var(--text-muted); }
    .text-secondary { color: var(--text-secondary); }
    .text-accent { color: var(--accent); }
    .text-success { color: var(--success); }
    .text-warning { color: var(--warning); }
    .text-error { color: var(--error); }
    .text-center { text-align: center; }
    .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .grid-auto { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: var(--space-4); }

    /* ============================================================ RESPONSIVE / DRAWER */
    .drawer-backdrop {
        position: fixed; inset: 0; z-index: var(--z-drawer-backdrop);
        background: var(--backdrop);
        backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);
    }

    @media (max-width: 900px) {
        .app-frame, body.sidebar-collapsed .app-frame { margin-left: 0; }
        .sidebar {
            transform: translateX(-100%);
            z-index: var(--z-drawer);
            box-shadow: var(--shadow-lg);
            width: var(--sidebar-w) !important;
        }
        body.drawer-open .sidebar { transform: translateX(0); }
        body.sidebar-collapsed .sidebar { width: var(--sidebar-w) !important; }
        body.sidebar-collapsed .sidebar .nav-label,
        body.sidebar-collapsed .sidebar .sidebar-brand-text,
        body.sidebar-collapsed .sidebar .nav-section-label { opacity: 1 !important; width: auto !important; height: auto !important; padding: revert; overflow: visible; }
        body.sidebar-collapsed .sidebar .nav-item { justify-content: flex-start; }
        .hamburger { display: inline-flex; }
        .sidebar-foot { display: none; }
        .topbar-mobile-logo { display: inline-flex; }
        .palette-trigger { min-width: 0; }
        .palette-trigger .pt-label, .palette-trigger .kbd { display: none; }
    }
    @media (min-width: 901px) {
        .drawer-backdrop { display: none !important; }
    }
    @media (max-width: 520px) {
        .stats-grid { grid-template-columns: 1fr; }
        .palette-trigger { width: 38px; min-width: 38px; padding: 0; justify-content: center; }
        .palette-trigger .pt-label, .palette-trigger .kbd { display: none; }
    }
</style>
