import { initFlashToasts } from './modules/toast.js';
import { initModals } from './modules/modal.js';
import { initLoadingForms } from './modules/loading.js';

document.addEventListener('DOMContentLoaded', () => {
    initFlashToasts();
    initModals();
    initLoadingForms();
});
