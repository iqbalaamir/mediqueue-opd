/**
 * Booking details page — fee quote AJAX and OTP send/verify.
 */
import { postJson, handleRequestError } from './http.js';
import { showToast } from './toast.js';

function debounce(fn, ms) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
}

export function initBookingSchedule() {
    const form = document.querySelector('[data-booking-details]');
    if (!form) return;

    const feeQuoteUrl = form.dataset.feeQuoteUrl;
    const otpSendUrl = form.dataset.otpSendUrl;
    const otpVerifyUrl = form.dataset.otpVerifyUrl;
    const otpRequired = form.dataset.otpRequired === 'true';
    const slotUuid = form.dataset.slotUuid;

    const mobileInput = form.querySelector('[data-fee-mobile]');
    const feePanel = document.getElementById('fee-quote-panel');
    const feeLoading = feePanel?.querySelector('[data-fee-loading]');
    const feeVisitType = feePanel?.querySelector('[data-fee-visit-type]');
    const feeAmount = feePanel?.querySelector('[data-fee-amount]');
    const feeDue = feePanel?.querySelector('[data-fee-due]');

    const fetchFeeQuote = debounce(async () => {
        const mobile = mobileInput?.value?.replace(/\D/g, '') ?? '';

        if (mobile.length < 10 || !feePanel) {
            feePanel?.setAttribute('hidden', '');
            return;
        }

        feePanel.removeAttribute('hidden');
        feeLoading?.removeAttribute('hidden');

        try {
            const data = await postJson(feeQuoteUrl, {
                slot_uuid: slotUuid,
                patient_mobile: mobile,
            });

            if (feeVisitType) feeVisitType.textContent = data.visit_type_label ?? '—';
            if (feeAmount) feeAmount.textContent = data.formatted_fee ?? '—';
            if (feeDue) feeDue.textContent = data.formatted_due ?? '—';
        } catch (error) {
            handleRequestError(error, 'Could not fetch fee quote.');
        } finally {
            feeLoading?.setAttribute('hidden', '');
        }
    }, 400);

    mobileInput?.addEventListener('input', fetchFeeQuote);

    if (mobileInput?.value?.replace(/\D/g, '').length >= 10) {
        fetchFeeQuote();
    }

    if (otpRequired) {
        const sendBtn = form.querySelector('[data-otp-send]');
        const verifyBtn = form.querySelector('[data-otp-verify]');
        const otpInput = form.querySelector('[data-otp-input]');
        const verifiedMsg = form.querySelector('[data-otp-verified]');

        sendBtn?.addEventListener('click', async () => {
            const mobile = mobileInput?.value?.replace(/\D/g, '') ?? '';

            if (mobile.length < 10) {
                showToast('Enter a valid 10-digit mobile number first.', 'error');
                return;
            }

            sendBtn.disabled = true;

            try {
                const data = await postJson(otpSendUrl, { mobile });
                showToast(data.message ?? 'OTP sent.', 'success');
            } catch (error) {
                handleRequestError(error, 'Could not send OTP.');
            } finally {
                sendBtn.disabled = false;
            }
        });

        verifyBtn?.addEventListener('click', async () => {
            const mobile = mobileInput?.value?.replace(/\D/g, '') ?? '';
            const otp = otpInput?.value?.trim() ?? '';

            if (mobile.length < 10) {
                showToast('Enter a valid mobile number first.', 'error');
                return;
            }

            if (otp.length !== 6) {
                showToast('Enter the 6-digit OTP.', 'error');
                return;
            }

            verifyBtn.disabled = true;

            try {
                const data = await postJson(otpVerifyUrl, { mobile, otp });
                verifiedMsg?.removeAttribute('hidden');
                showToast(data.message ?? 'Verified.', 'success');
            } catch (error) {
                verifiedMsg?.setAttribute('hidden', '');
                handleRequestError(error, 'OTP verification failed.');
            } finally {
                verifyBtn.disabled = false;
            }
        });
    }
}

export default { initBookingSchedule };
