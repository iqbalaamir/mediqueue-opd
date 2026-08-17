import { initFlashToasts } from './modules/toast.js';
import { initModals } from './modules/modal.js';
import { initLoadingForms } from './modules/loading.js';
import { initBookingSchedule } from './modules/booking-schedule.js';

document.addEventListener('DOMContentLoaded', () => {
    initFlashToasts();
    initModals();
    initLoadingForms();

    if (document.querySelector('[data-booking-details]')) {
        initBookingSchedule();
    }
});
