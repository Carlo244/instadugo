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
window.Echo.channel('donations')
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
    });

// Real-time listener for blood requests (hospital + users)
window.Echo.channel('blood-requests')
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

        const today = new Date().toISOString().split('T')[0];
        const donationDate = donation.donation_date;
        let tab = 'upcoming';
        if (donationDate === today) tab = 'today';
        else if (new Date(donationDate) < new Date(today)) tab = 'history';

        const containerId = `hospital-donations-${tab}`;
        const container = document.getElementById(containerId);

        // Optimistic insert: create a temporary row and prepend it to the table body
        if (container) {
            const parser = new DOMParser();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = `
                <table class="table mb-0">
                    <tbody>
                        <tr class="flash-new" data-temp-row="1">
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-blood-subtle rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                                        <span class="text-blood fw-bold small">${(donation.user?.name || '?').charAt(0).toUpperCase()}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">${donation.user?.name || 'Unknown'}</div>
                                        <small class="text-muted" style="font-size:0.75rem;">${donation.user?.email || ''}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">${donation.donation_date === today ? 'Today' : donation.donation_date}</div>
                                <div class="text-muted small"><i class="bi bi-clock me-1"></i>${donation.donation_time}</div>
                            </td>
                            <td class="text-center"><span class="badge bg-danger-subtle text-danger border px-3 py-2" style="min-width:50px;">${donation.blood_type}</span></td>
                            <td><span class="badge bg-blood px-3 py-2 rounded-pill text-white"><i class="bi bi-calendar-event me-1"></i> ${donation.status}</span></td>
                            ${tab === 'today' ? `<td class="text-end pe-3"><span class="text-muted small">Processing...</span></td>` : ''}
                        </tr>
                    </tbody>
                </table>
            `;

            // Find the table body inside the existing container and prepend the row
            const existingTable = container.querySelector('table');
            if (existingTable) {
                const tbody = existingTable.querySelector('tbody');
                if (tbody) {
                    const firstRow = tempDiv.querySelector('tr');
                    tbody.insertBefore(firstRow, tbody.firstChild);
                    // remove flash class after animation completes
                    setTimeout(() => {
                        firstRow.classList.remove('flash-new');
                        firstRow.removeAttribute('data-temp-row');
                    }, 3000);
                }
            }
        }

        // Always fetch authoritative partial and replace container to reconcile
        console.log('[donation-updated] fetching partial for tab:', tab);
        const resp = await fetch(`/hospital/donations?ajax=1&tab=${tab}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        console.log('[donation-updated] fetch finished', resp.status);
        if (!resp.ok) {
            console.error('[donation-updated] Failed to fetch updated donations partial', resp.status);
            // fallback: reload the page so the hospital sees the update
            try { location.reload(); } catch (e) { /* noop */ }
            return;
        }

        const html = await resp.text();
        if (container) {
            container.innerHTML = html;
        } else {
            console.warn('[donation-updated] container not found:', containerId, ' — reloading page');
            try { location.reload(); } catch (e) { /* noop */ }
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