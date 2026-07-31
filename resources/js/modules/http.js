/**
 * HTTP helpers with CSRF and JSON handling.
 */
import { showToast } from './toast.js';
import { hideLoading } from './loading.js';

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

export async function request(url, options = {}) {
    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers ?? {}),
    };

    if (options.body && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(options.body);
    }

    if (['POST', 'PUT', 'PATCH', 'DELETE'].includes((options.method ?? 'GET').toUpperCase())) {
        headers['X-CSRF-TOKEN'] = csrfToken();
    }

    const response = await fetch(url, { ...options, headers });
    const contentType = response.headers.get('content-type') ?? '';
    const data = contentType.includes('application/json') ? await response.json() : await response.text();

    if (!response.ok) {
        const message = typeof data === 'object' && data?.message
            ? data.message
            : 'Something went wrong. Please try again.';

        throw { status: response.status, message, data };
    }

    return data;
}

export async function getJson(url) {
    return request(url, { method: 'GET' });
}

export async function postJson(url, body = {}) {
    return request(url, { method: 'POST', body });
}

export function handleRequestError(error, fallback = 'Request failed.') {
    hideLoading();
    showToast(error?.message ?? fallback, 'error');
}

export default { request, getJson, postJson, handleRequestError };
