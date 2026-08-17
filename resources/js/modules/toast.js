/**
 * Toast notification system.
 */
const container = () => document.getElementById('toast-container');

const icons = {
    success: '<svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
    error: '<svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
    info: '<svg class="h-5 w-5 text-brand-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>',
};

const typeClasses = {
    success: 'border-emerald-200 bg-white',
    error: 'border-red-200 bg-white',
    info: 'border-brand-200 bg-white',
};

export function showToast(message, type = 'info', duration = 5000) {
    const root = container();
    if (!root || !message) return;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-start gap-3 rounded-xl border p-4 shadow-lg transition ${typeClasses[type] ?? typeClasses.info}`;
    toast.innerHTML = `
        <span class="mt-0.5 shrink-0">${icons[type] ?? icons.info}</span>
        <p class="flex-1 text-sm text-slate-700">${escapeHtml(message)}</p>
        <button type="button" class="shrink-0 text-slate-400 hover:text-slate-600" aria-label="Dismiss">&times;</button>
    `;

    const dismiss = () => {
        toast.classList.add('opacity-0', 'translate-x-2');
        setTimeout(() => toast.remove(), 200);
    };

    toast.querySelector('button').addEventListener('click', dismiss);
    root.appendChild(toast);

    if (duration > 0) {
        setTimeout(dismiss, duration);
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

export function initFlashToasts() {
    const body = document.body;
    const success = body.dataset.flashSuccess;
    const error = body.dataset.flashError;
    const info = body.dataset.flashInfo;

    if (success) showToast(success, 'success');
    if (error) showToast(error, 'error');
    if (info) showToast(info, 'info');
}

export default { showToast, initFlashToasts };
