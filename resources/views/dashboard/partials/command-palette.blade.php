{{-- Command palette: ⌘K / Ctrl+K. Pages register extra items via:
       window.dispatchEvent(new CustomEvent('domo-palette:register', { detail: [{...}] }))
     or Alpine.store('palette').register([{...}]).
     Item shape: { id, label, hint?, group?, icon?, href?, action? } --}}
<style>
    .palette-overlay {
        position: fixed; inset: 0; z-index: var(--z-palette);
        display: flex; align-items: flex-start; justify-content: center;
        padding-top: 12vh; padding-left: var(--space-4); padding-right: var(--space-4);
        background: var(--backdrop);
        backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    }
    .palette {
        width: 100%; max-width: 600px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: domo-pop var(--t-base) both;
    }
    .palette-search { display: flex; align-items: center; gap: var(--space-3); padding: var(--space-4); border-bottom: 1px solid var(--border-subtle); }
    .palette-search svg { width: 18px; height: 18px; stroke: var(--text-muted); flex-shrink: 0; }
    .palette-search input {
        flex: 1; background: transparent; border: none; outline: none;
        color: var(--text-primary); font-size: 1rem; font-family: var(--font-sans);
    }
    .palette-search input::placeholder { color: var(--text-muted); }
    .palette-search .kbd { flex-shrink: 0; }
    .palette-list { max-height: 56vh; overflow-y: auto; padding: var(--space-2); }
    .palette-group-label {
        font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em;
        color: var(--text-muted); font-family: var(--font-mono);
        padding: var(--space-3) var(--space-3) var(--space-2);
    }
    .palette-item {
        display: flex; align-items: center; gap: var(--space-3);
        padding: var(--space-3); border-radius: var(--radius-md);
        cursor: pointer; color: var(--text-secondary);
        text-decoration: none; width: 100%; text-align: left;
        border: none; background: transparent; font-size: 0.9rem;
    }
    .palette-item .pi-icon { width: 18px; height: 18px; display: grid; place-items: center; flex-shrink: 0; }
    .palette-item .pi-icon svg { width: 17px; height: 17px; stroke: currentColor; }
    .palette-item .pi-label { flex: 1; color: var(--text-primary); }
    .palette-item .pi-hint { font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono); }
    .palette-item.is-active { background: var(--accent-soft); color: var(--accent); }
    .palette-item.is-active .pi-label { color: var(--accent); }
    .palette-item:hover { background: var(--hover-overlay); }
    /* Light theme: --accent (#e0241a) on accent-soft only reaches ~3.76:1 (fails
       WCAG 2.2 AA). Use the darker --accent-active (~5.92:1) for the active option
       text so the highlighted result stays legible. Dark theme already passes. */
    [data-theme="light"] .palette-item.is-active,
    [data-theme="light"] .palette-item.is-active .pi-label { color: var(--accent-active); }
    .palette-empty { padding: var(--space-8) var(--space-4); text-align: center; color: var(--text-muted); font-size: 0.875rem; }
    .palette-foot {
        display: flex; align-items: center; gap: var(--space-4);
        padding: var(--space-2) var(--space-4);
        border-top: 1px solid var(--border-subtle);
        font-size: 0.72rem; color: var(--text-muted);
    }
    .palette-foot .pf-key { display: inline-flex; align-items: center; gap: var(--space-1); }
    .palette-foot .kbd { font-size: 0.65rem; padding: 2px 5px; }
</style>

<div
    x-data="domoPalette()"
    x-show="open"
    x-cloak
    class="palette-overlay"
    @keydown.window.meta.k.prevent="toggle()"
    @keydown.window.ctrl.k.prevent="toggle()"
    @domo-palette:open.window="show()"
    @keydown.escape.stop.prevent="hide()"
    @keydown.down.prevent="move(1)"
    @keydown.up.prevent="move(-1)"
    @keydown.enter.prevent="choose()"
    @keydown.tab="trap($event)"
    @click.self="hide()"
    x-transition.opacity
    role="dialog" aria-modal="true" aria-label="Command palette"
>
    <div class="palette" x-ref="panel" @click.stop>
        <div class="palette-search">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                x-ref="input"
                x-model="query"
                @input="active = 0"
                placeholder="Search pages, tables, models, actions…"
                role="combobox"
                :aria-expanded="flat.length > 0 ? 'true' : 'false'"
                aria-controls="palette-listbox"
                :aria-activedescendant="flat[active] ? 'palette-opt-' + active : null"
                autocomplete="off" autocapitalize="off" spellcheck="false"
            />
            <kbd class="kbd">esc</kbd>
        </div>

        <div class="palette-list" id="palette-listbox" role="listbox" aria-label="Results">
            <template x-for="(group, gi) in grouped" :key="group.name">
                <div>
                    <div class="palette-group-label" x-text="group.name" x-show="group.name"></div>
                    <template x-for="item in group.items" :key="item.id">
                        {{-- Non-focusable option: Tab must not land here. The
                             aria-activedescendant on the combobox input is the
                             single source of truth for the highlighted result.
                             Arrow keys move(), Enter chooses (see shell-script). --}}
                        <div
                            class="palette-item"
                            :id="'palette-opt-' + item._i"
                            :class="{ 'is-active': item._i === active }"
                            role="option"
                            :aria-selected="item._i === active ? 'true' : 'false'"
                            @mousemove="active = item._i"
                            @click="select(item)"
                        >
                            <span class="pi-icon" x-html="item.icon || defaultIcon" aria-hidden="true"></span>
                            <span class="pi-label" x-text="item.label"></span>
                            <span class="pi-hint" x-text="item.hint || ''"></span>
                        </div>
                    </template>
                </div>
            </template>
            <div class="palette-empty" x-show="flat.length === 0">
                No results for "<span x-text="query"></span>"
            </div>
        </div>

        <div class="palette-foot">
            <span class="pf-key"><kbd class="kbd">↑</kbd><kbd class="kbd">↓</kbd> navigate</span>
            <span class="pf-key"><kbd class="kbd">↵</kbd> open</span>
            <span class="pf-key"><kbd class="kbd">esc</kbd> close</span>
        </div>
    </div>
</div>
