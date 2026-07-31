/**
 * Modal dialog utilities.
 */
let activeModal = null;

function getModal(id) {
    return document.getElementById(id);
}

export function openModal(id) {
    const modal = getModal(id);
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    activeModal = modal;

    const focusable = modal.querySelector('[data-modal-close], button, a, input, select, textarea');
    focusable?.focus();
}

export function closeModal(id = null) {
    const modal = id ? getModal(id) : activeModal;
    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');

    if (activeModal === modal) {
        activeModal = null;
    }
}

export function confirmModal(message, onConfirm) {
    const modal = getModal('confirm-modal');
    const messageEl = document.getElementById('confirm-modal-message');
    const submitBtn = document.getElementById('confirm-modal-submit');

    if (!modal || !messageEl || !submitBtn) return;

    messageEl.textContent = message;

    const handler = () => {
        submitBtn.removeEventListener('click', handler);
        closeModal('confirm-modal');
        onConfirm?.();
    };

    submitBtn.replaceWith(submitBtn.cloneNode(true));
    document.getElementById('confirm-modal-submit')?.addEventListener('click', handler);
    openModal('confirm-modal');
}

export function initModals() {
    document.addEventListener('click', (event) => {
        const closeTrigger = event.target.closest('[data-modal-close]');
        if (closeTrigger) {
            const modal = closeTrigger.closest('[data-modal]');
            if (modal) closeModal(modal.id);
        }

        const openTrigger = event.target.closest('[data-modal-open]');
        if (openTrigger) {
            openModal(openTrigger.dataset.modalOpen);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && activeModal) {
            closeModal(activeModal.id);
        }
    });
}

export default { openModal, closeModal, confirmModal, initModals };
