import { initFlashToasts } from './modules/toast.js';
import { initModals } from './modules/modal.js';
import { initLoadingForms } from './modules/loading.js';
import { initBookingSchedule } from './modules/booking-schedule.js';
import { initQueueTracker } from './modules/queue-tracker.js';

document.addEventListener('DOMContentLoaded', () => {
    initFlashToasts();
    initModals();
    initLoadingForms();
    initQueueTracker();

    if (document.querySelector('[data-booking-details]')) {
        initBookingSchedule();
    }
});
