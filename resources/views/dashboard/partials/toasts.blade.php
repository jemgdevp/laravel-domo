{{-- Toast system. Trigger from any page with:
       window.domoToast('Saved!', 'success')   // type: success | error | info | warning
     or: window.dispatchEvent(new CustomEvent('domo-toast', { detail: { message, type, timeout } })) --}}
<style>
    .toast-region {
        position: fixed; top: calc(var(--topbar-h) + var(--space-3)); right: var(--space-4);
        z-index: var(--z-toast);
        display: flex; flex-direction: column; gap: var(--space-3);
        width: min(360px, calc(100vw - var(--space-6)));
        pointer-events: none;
    }
    .toast {
        pointer-events: auto;
        display: flex; align-items: flex-start; gap: var(--space-3);
        padding: var(--space-3) var(--space-4);
        background: var(--bg-elevated);
        border: 1px solid var(--border-color);
        border-left-width: 3px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        animation: domo-toast-in var(--t-base) both;
    }
    .toast .toast-icon { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
    .toast .toast-icon svg { width: 18px; height: 18px; stroke: currentColor; }
    .toast .toast-body { flex: 1; min-width: 0; }
    .toast .toast-msg { font-size: 0.85rem; color: var(--text-primary); line-height: 1.45; word-break: break-word; }
    .toast .toast-close {
        flex-shrink: 0; width: 22px; height: 22px; display: grid; place-items: center;
        border: none; background: transparent; color: var(--text-muted); cursor: pointer; border-radius: var(--radius-sm);
    }
    .toast .toast-close:hover { color: var(--text-primary); background: var(--hover-overlay); }
    .toast .toast-close svg { width: 13px; height: 13px; stroke: currentColor; }
    .toast.is-success { border-left-color: var(--success); } .toast.is-success .toast-icon { color: var(--success); }
    .toast.is-error   { border-left-color: var(--error); }   .toast.is-error .toast-icon { color: var(--error); }
    .toast.is-warning { border-left-color: var(--warning); } .toast.is-warning .toast-icon { color: var(--warning); }
    .toast.is-info    { border-left-color: var(--info); }    .toast.is-info .toast-icon { color: var(--info); }
</style>

<div class="toast-region" role="region" aria-label="Notifications">
    <template x-for="t in toasts" :key="t.id">
        <div class="toast" :class="'is-' + t.type" :role="(t.type === 'error' || t.type === 'warning') ? 'alert' : 'status'" :aria-live="(t.type === 'error' || t.type === 'warning') ? 'assertive' : 'polite'" aria-atomic="true">
            <span class="toast-icon" aria-hidden="true">
                <template x-if="t.type === 'success'"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></template>
                <template x-if="t.type === 'error'"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></template>
                <template x-if="t.type === 'warning'"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></template>
                <template x-if="t.type === 'info'"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg></template>
            </span>
            <div class="toast-body">
                <div class="toast-msg" x-text="t.message"></div>
            </div>
            <button type="button" class="toast-close" @click="dismissToast(t.id)" aria-label="Dismiss notification">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </template>
</div>
