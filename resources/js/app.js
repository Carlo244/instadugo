import './bootstrap';
import './echo';
import { initSidebar } from './modules/sidebar-toggle';
import { initDonationScheduleSlots } from './modules/slot-generator';
import { initSendRequestModal, initAcceptInvitationModal } from './modules/modal-handlers';
import { initPasswordToggle, initModalReset } from './modules/password-toggle';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDonationScheduleSlots();
    initSendRequestModal();
    initAcceptInvitationModal();
    initPasswordToggle();
    initModalReset('addUserModal', 'addUserForm');
});

// Global Real-time Listener for donations
window.Echo.private('donations')
    .listen('.App\\Events\\DonationCreated', (e) => { 
        console.log('New donation detected:', e.donation);
        window.dispatchEvent(new CustomEvent('donation-updated', { detail: e.donation }));
        
        // only show toast to hospitals (they care); donors just see their dashboard update
        if (window.hospitalAdminId) {
            const toastContainer = document.getElementById('toast-container');
            if (toastContainer) {
                const toastHTML = `
                <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            New donation scheduled at ${e.donation.donation_time}.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;
                toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                setTimeout(() => toastContainer.lastElementChild?.remove(), 5000);
            }
        }
    })
    .listen('.App\\Events\\DonationStatusUpdated', (e) => {
        console.log('Donation status updated:', e.donation, e.fromStatus, e.toStatus);
        window.dispatchEvent(new CustomEvent('donation-updated', { detail: e.donation }));

        if (window.hospitalAdminId) {
            const toastContainer = document.getElementById('toast-container');
            if (toastContainer) {
                const toastClass = e.toStatus === 'cancelled' ? 'text-bg-warning' : 'text-bg-primary';
                const toastHTML = `
                <div class="toast align-items-center ${toastClass} border-0 show mb-2" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            Donation status changed: ${e.fromStatus} → ${e.toStatus}.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;
                toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                setTimeout(() => toastContainer.lastElementChild?.remove(), 5000);
            }
        }
    });

// Real-time listener for blood requests (hospital + users)
window.Echo.private('blood-requests')
    .listen('.App\\Events\\BloodRequestCreated', (e) => {
        if (!window.hospitalAdminId) {
            return;
        }

        console.log('New blood request detected (hospital):', e.bloodRequest);
        window.dispatchEvent(new CustomEvent('bloodrequest-updated', { detail: e.bloodRequest }));
        const toastContainer = document.getElementById('toast-container');
        if (toastContainer) {
            const toastHTML = `
            <div class="toast align-items-center text-bg-info border-0 show mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        New blood request created for ${e.bloodRequest.blood_type}.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            setTimeout(() => toastContainer.lastElementChild?.remove(), 5000);
        }
    })
    .listen('.App\\Events\\BloodRequestStatusUpdated', (e) => {
        console.log('Blood request status updated:', e.bloodRequest, e.fromStatus, e.toStatus);
        window.dispatchEvent(new CustomEvent('bloodrequest-status-updated', {
            detail: {
                bloodRequest: e.bloodRequest,
                fromStatus: e.fromStatus,
                toStatus: e.toStatus,
            },
        }));
    })
    .listen('.App\\Events\\BloodRequestPriorityUpdated', (e) => {
        console.log('Blood request priority updated:', e.bloodRequest, e.fromUrgency, e.toUrgency);
        window.dispatchEvent(new CustomEvent('bloodrequest-priority-updated', {
            detail: {
                bloodRequest: e.bloodRequest,
                fromUrgency: e.fromUrgency,
                toUrgency: e.toUrgency,
            },
        }));
    });

// Hospital-side: optimistic insert + AJAX refresh when a relevant donation is created
window.addEventListener('donation-updated', async (ev) => {
    try {
        const donation = ev.detail;
        if (!donation) return;

        console.log('[donation-updated] event received', donation);

        // if this is the donor's own donation, refresh dashboard partial
        if (window.currentUserId && String(window.currentUserId) === String(donation.user_id)) {
            console.log('[donation-updated] donor match, refreshing dashboard');
            const donorContainer = document.getElementById('dashboard-donations');
            if (donorContainer) {
                const resp = await fetch('/user/dashboard?ajax=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (resp.ok) {
                    donorContainer.innerHTML = await resp.text();
                } else {
                    console.warn('[donation-updated] donor AJAX failed', resp.status);
                }
            }
            // continue processing in case user is also hospital or to add toast
        }

        if (!window.hospitalAdminId) {
            console.warn('[donation-updated] no window.hospitalAdminId present, skipping update');
            return;
        }

        // Donation payload may sometimes nest ids differently when serialized — normalize
        const donationHospitalId = donation.hospital_admin_id ?? donation.hospitalAdminId ?? donation.hospital_admin?.id ?? donation.hospital_admin_id?.id;
        console.log('[donation-updated] hospital ids -> page:', window.hospitalAdminId, 'donation:', donationHospitalId);
        if (String(window.hospitalAdminId) !== String(donationHospitalId)) {
            console.log('[donation-updated] donation not for this hospital, ignoring');
            return;
        }

        const refreshTargets = [
            { tab: 'today', containerId: 'hospital-donations-today' },
            { tab: 'upcoming', containerId: 'hospital-donations-upcoming' },
            { tab: 'history', containerId: 'hospital-donations-history' },
        ];

        for (const target of refreshTargets) {
            const container = document.getElementById(target.containerId);
            if (!container) continue;

            console.log('[donation-updated] fetching partial for tab:', target.tab);
            const resp = await fetch(`/hospital/donations?ajax=1&tab=${target.tab}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!resp.ok) {
                console.warn('[donation-updated] failed to refresh tab:', target.tab, resp.status);
                continue;
            }

            container.innerHTML = await resp.text();
        }
    } catch (err) {
        console.error('Error updating hospital donations via AJAX:', err);
    }
});

// ===== blood requests =====
window.addEventListener('bloodrequest-updated', async (ev) => {
    try {
        const br = ev.detail;
        console.log('[bloodrequest-updated] event', br);
        if (!br) return;
        if (!window.hospitalAdminId) return;
        if (String(window.hospitalAdminId) !== String(br.hospital_admin_id)) {
            console.log('[bloodrequest-updated] not for this hospital');
            return;
        }
        const level = br.urgency || 'Emergency';
        const containerId = `bloodrequests-live-${level.toLowerCase()}`;
        const container = document.getElementById(containerId);

        // optimistic placeholder row
        if (container) {
            const existingTable = container.querySelector('table');
            if (existingTable) {
                const tbody = existingTable.querySelector('tbody');
                if (tbody) {
                    const row = document.createElement('tr');
                    row.className = 'flash-new';
                    row.innerHTML = `
                        <td colspan="6" class="text-center small text-muted py-2">
                            New request for ${br.blood_type} (${level})
                        </td>`;
                    tbody.insertBefore(row, tbody.firstChild);
                    setTimeout(() => row.classList.remove('flash-new'), 2500);
                }
            }
        }

        console.log('[bloodrequest-updated] fetching partial for level', level);
        const resp = await fetch(`/hospital/requests?ajax=1&level=${level}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        console.log('[bloodrequest-updated] fetch status', resp.status);
        if (!resp.ok) {
            console.error('[bloodrequest-updated] AJAX failed', resp.status);
            location.reload();
            return;
        }
        const html = await resp.text();
        if (container) {
            container.innerHTML = html;
            // recalc individual and global badge counts
            const levels = ['emergency','high','normal'];
            let total = 0;
            levels.forEach(lvl => {
                const tbl = document.getElementById(`bloodrequests-live-${lvl}`);
                const count = tbl ? tbl.querySelectorAll('tbody tr').length : 0;
                total += count;
                const btn = document.querySelector(`#priority-tabs button[data-bs-target="#priority-${lvl}"] h2`);
                if (btn) btn.textContent = count;
            });
            const activeBadge = document.querySelector('.badge.bg-blood-gradient');
            if (activeBadge) {
                activeBadge.textContent = ` Active: ${total}`;
            }
        } else {
            location.reload();
        }
    } catch (err) {
        console.error('[bloodrequest-updated] error', err);
    }
});

window.addEventListener('bloodrequest-status-updated', async (ev) => {
    try {
        const payload = ev.detail;
        const br = payload?.bloodRequest;
        const fromStatus = payload?.fromStatus;
        const toStatus = payload?.toStatus;

        if (!br) {
            return;
        }

        const isForCurrentUser = window.currentUserId && (
            String(window.currentUserId) === String(br.user_id) ||
            String(window.currentUserId) === String(br.receiver_id)
        );

        if (isForCurrentUser) {
            const toastContainer = document.getElementById('toast-container');
            if (toastContainer) {
                const toastHTML = `
                <div class="toast align-items-center text-bg-primary border-0 show mb-2" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            Your request #${br.id} moved from ${fromStatus} to ${toStatus}.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;
                toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                setTimeout(() => toastContainer.lastElementChild?.remove(), 5000);
            }

            const onUserPage = window.location.pathname.startsWith('/user');
            if (onUserPage) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }

        if (!window.hospitalAdminId) {
            return;
        }

        if (String(window.hospitalAdminId) !== String(br.hospital_admin_id)) {
            return;
        }

        const levels = ['Emergency', 'High', 'Normal'];
        for (const level of levels) {
            const container = document.getElementById(`bloodrequests-live-${level.toLowerCase()}`);
            if (!container) {
                continue;
            }

            const resp = await fetch(`/hospital/requests?ajax=1&level=${encodeURIComponent(level)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (resp.ok) {
                container.innerHTML = await resp.text();
            }
        }

        const historyContainer = document.getElementById('bloodrequests-history');
        if (historyContainer) {
            const historyResp = await fetch('/hospital/requests?ajax=1&level=History', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (historyResp.ok) {
                historyContainer.innerHTML = await historyResp.text();
            }
        }

        let total = 0;
        ['emergency', 'high', 'normal'].forEach((lvl) => {
            const tbl = document.getElementById(`bloodrequests-live-${lvl}`);
            const count = tbl ? tbl.querySelectorAll('tbody tr.request-row').length : 0;
            total += count;

            const btn = document.querySelector(`#priority-tabs button[data-bs-target="#priority-${lvl}"] h2`);
            if (btn) {
                btn.textContent = count;
            }
        });

        const activeBadge = document.querySelector('.badge.bg-blood-gradient strong');
        if (activeBadge) {
            activeBadge.textContent = total;
        }
    } catch (err) {
        console.error('[bloodrequest-status-updated] error', err);
    }
});

window.addEventListener('bloodrequest-priority-updated', async (ev) => {
    try {
        const payload = ev.detail;
        const br = payload?.bloodRequest;
        const fromUrgency = payload?.fromUrgency;
        const toUrgency = payload?.toUrgency;

        if (!br || !window.hospitalAdminId) {
            return;
        }

        if (String(window.hospitalAdminId) !== String(br.hospital_admin_id)) {
            return;
        }

        const toastContainer = document.getElementById('toast-container');
        if (toastContainer) {
            const toastHTML = `
            <div class="toast align-items-center text-bg-info border-0 show mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        Request #${br.id} priority changed: ${fromUrgency} -> ${toUrgency}.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            setTimeout(() => toastContainer.lastElementChild?.remove(), 5000);
        }

        const levels = ['Emergency', 'High', 'Normal'];
        for (const level of levels) {
            const container = document.getElementById(`bloodrequests-live-${level.toLowerCase()}`);
            if (!container) {
                continue;
            }

            const resp = await fetch(`/hospital/requests?ajax=1&level=${encodeURIComponent(level)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (resp.ok) {
                container.innerHTML = await resp.text();
            }
        }

        let total = 0;
        ['emergency', 'high', 'normal'].forEach((lvl) => {
            const tbl = document.getElementById(`bloodrequests-live-${lvl}`);
            const count = tbl ? tbl.querySelectorAll('tbody tr.request-row').length : 0;
            total += count;

            const btn = document.querySelector(`#priority-tabs button[data-bs-target="#priority-${lvl}"] h2`);
            if (btn) {
                btn.textContent = count;
            }
        });

        const activeBadge = document.querySelector('.badge.bg-blood-gradient strong');
        if (activeBadge) {
            activeBadge.textContent = total;
        }
    } catch (err) {
        console.error('[bloodrequest-priority-updated] error', err);
    }
});