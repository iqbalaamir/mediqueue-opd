/**
 * Global loading overlay for form submissions and async actions.
 */
const overlay = () => document.getElementById('loading-overlay');

let loadingCount = 0;

export function showLoading() {
    const el = overlay();
    if (!el) return;

    loadingCount += 1;
    el.classList.remove('hidden');
    el.classList.add('flex');
    el.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

export function hideLoading() {
    const el = overlay();
    if (!el) return;

    loadingCount = Math.max(0, loadingCount - 1);

    if (loadingCount === 0) {
        el.classList.add('hidden');
        el.classList.remove('flex');
        el.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }
}

export function initLoadingForms() {
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-loading-form]');
        if (!form || form.dataset.loadingDisabled === 'true') return;
        showLoading();
    });
}

export default { showLoading, hideLoading, initLoadingForms };
