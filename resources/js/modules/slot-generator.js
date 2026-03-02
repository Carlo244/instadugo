/**
 * Generate time slot options
 */
export function generateTimeSlots(occupiedTimes, selectedDate, timeSelect) {
    timeSelect.innerHTML = '<option value="">Select Time</option>';
    const now = new Date();
    const today = new Date().toISOString().split('T')[0];
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();

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
            option.text += " (Unavailable)";
        } else if (selectedDate === today && (h < currentHour || (h === currentHour && m <= currentMinute))) {
            option.disabled = true;
            option.style.display = "none";
        }
        timeSelect.appendChild(option);
    }
}

/**
 * Initialize slot updater for donation schedule page
 */
export function initDonationScheduleSlots() {
    const hospitalSelect = document.getElementById('hospital_admin_id');
    const dateInput = document.getElementById('donation_date');
    const timeSelect = document.getElementById('donation_time');

    if (!hospitalSelect || !dateInput || !timeSelect) return;

    const updateSlots = async function() {
        const hospitalId = hospitalSelect.value;
        const selectedDate = dateInput.value;
        if (!hospitalId || !selectedDate) return;

        try {
            const base = window.routeOccupiedTimes || '/user/donations/occupied-times';
            const url = `${base}?hospital_id=${hospitalId}&date=${selectedDate}`;
            const response = await fetch(url);
            const occupiedTimes = await response.json();
            generateTimeSlots(occupiedTimes, selectedDate, timeSelect);
        } catch (error) {
            console.error('Error fetching occupied times:', error);
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
        }
    };

    hospitalSelect.addEventListener('change', updateSlots);
    dateInput.addEventListener('change', updateSlots);

    // Store updateSlots globally so it can be called from Echo listener
    window.updateSlotsOnDonate = updateSlots;
}