/**
 * Live queue polling for patient tracking page.
 */
import { getJson, handleRequestError } from './http.js';

export function initQueueTracker() {
    const root = document.querySelector('[data-queue-tracker]');
    if (!root) return;

    const url = root.dataset.snapshotUrl;
    const tokenEl = document.getElementById('queue-token');
    const statusEl = document.getElementById('queue-status');
    const aheadEl = document.getElementById('queue-ahead');
    const etaEl = document.getElementById('queue-eta');
    const servingEl = document.getElementById('queue-serving');

    let intervalMs = 5000;

    async function poll() {
        try {
            const data = await getJson(url);

            if (data.status !== 'active') {
                return;
            }

            intervalMs = data.poll_interval_ms || intervalMs;

            if (tokenEl) tokenEl.textContent = data.token_number ?? '—';
            if (statusEl) statusEl.textContent = data.queue_status_label ?? '';
            if (aheadEl) aheadEl.textContent = data.patients_ahead ?? 0;
            if (etaEl) etaEl.textContent = `${data.eta_minutes ?? '—'} min`;
            if (servingEl) servingEl.textContent = data.currently_serving ?? '—';
        } catch (error) {
            handleRequestError(error, 'Unable to refresh queue.');
        }
    }

    poll();
    setInterval(poll, intervalMs);
}

export default { initQueueTracker };
