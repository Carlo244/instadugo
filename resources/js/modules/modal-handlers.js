/**
 * Handle send request modal functionality
 */
export function initSendRequestModal() {
    const sendRequestModal = document.getElementById('sendRequestModal');
    if (!sendRequestModal) return;

    sendRequestModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const donorId = button.getAttribute('data-donor-id');
        const maskId = button.getAttribute('data-mask-id');

        const hiddenInput = sendRequestModal.querySelector('#donorId');
        const displaySpan = sendRequestModal.querySelector('#displayDonorId');

        if (hiddenInput) hiddenInput.value = donorId;
        if (displaySpan) displaySpan.textContent = maskId;
    });
}


/**
 * Handle accept invitation modal with dynamic slot fetching
 */
export function initAcceptInvitationModal() {
    const acceptModal = document.getElementById('acceptInvitationModal');
    if (!acceptModal) return;

    const modalDateInput = document.getElementById('modal_donation_date');
    const modalTimeSelect = document.getElementById('modal_donation_time');
    const modalHospitalHidden = document.getElementById('modal_hospital_id');

    if (!modalDateInput || !modalTimeSelect || !modalHospitalHidden) return;

    // When modal opens
    acceptModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const requestId = button.getAttribute('data-request-id');
        const hospitalName = button.getAttribute('data-hospital');
        const requester = button.getAttribute('data-requester');
        const hospitalId = button.getAttribute('data-hospital-id');

        const form = acceptModal.querySelector('#acceptForm');
        if (form) {
            form.action = form.action.replace(/\/\d+$/, `/${requestId}`);
        }

        const reqName = acceptModal.querySelector('#reqName');
        const hospName = acceptModal.querySelector('#hospName');
        if (reqName) reqName.textContent = requester;
        if (hospName) hospName.textContent = hospitalName;

        modalHospitalHidden.value = hospitalId;
        modalDateInput.value = '';
        modalTimeSelect.innerHTML = '<option value="">Select a date first</option>';
    });

    // Update slots when date changes
    modalDateInput.addEventListener('change', async function() {
        const hospitalId = modalHospitalHidden.value;
        const selectedDate = this.value;

        if (!hospitalId || !selectedDate) return;

        modalTimeSelect.innerHTML = '<option value="">Loading slots...</option>';

        try {
            const base = window.routeOccupiedTimes || '/user/donations/occupied-times';
            const url = `${base}?hospital_id=${hospitalId}&date=${selectedDate}`;
            const response = await fetch(url);
            const occupiedTimes = await response.json();
            generateModalTimeSlots(occupiedTimes, selectedDate, modalTimeSelect);
        } catch (error) {
            console.error('Error fetching occupied times:', error);
            modalTimeSelect.innerHTML = '<option value="">Error loading slots</option>';
        }
    });
}

/**
 * Handle hospital request modal handoff (Details -> Match Donors)
 */
export function initHospitalRequestModalFlow() {
    document.addEventListener('click', function(event) {
        const trigger = event.target.closest('.js-open-match-from-details');
        if (!trigger) return;

        const currentModalEl = trigger.closest('.modal');
        const targetSelector = trigger.getAttribute('data-match-target');
        if (!currentModalEl || !targetSelector) return;

        const targetModalEl = document.querySelector(targetSelector);
        if (!targetModalEl || typeof bootstrap === 'undefined') return;

        event.preventDefault();

        const currentModal = bootstrap.Modal.getOrCreateInstance(currentModalEl);
        const targetModal = bootstrap.Modal.getOrCreateInstance(targetModalEl);

        const onHidden = () => {
            currentModalEl.removeEventListener('hidden.bs.modal', onHidden);
            targetModal.show();
        };

        currentModalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });
        currentModal.hide();
    });
}

/**
 * Generate time slot options for modal
 */
function generateModalTimeSlots(occupiedTimes, selectedDate, timeSelect) {
    timeSelect.innerHTML = '<option value="">Select Time</option>';
    const now = new Date();
    const today = new Date().toISOString().split('T')[0];

    let start = 8 * 60; // 8:00 AM
    let end = 16 * 60; // 4:00 PM
    let interval = 30;

    for (let minutes = start; minutes <= end; minutes += interval) {
        let h = Math.floor(minutes / 60);
        let m = minutes % 60;
        let timeValue = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':00';

        let option = document.createElement("option");
        option.value = timeValue;
        option.text = timeValue;

        if (occupiedTimes.includes(timeValue)) {
            option.disabled = true;
            option.text += " (Full)";
        } else if (selectedDate === today && (h < now.getHours() || (h === now.getHours() && m <= now.getMinutes()))) {
            option.style.display = "none";
        }

        timeSelect.appendChild(option);
    }
}
