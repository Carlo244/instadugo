import './bootstrap';
import './echo';
import { initSidebar } from './modules/sidebar-toggle';
import { initDonationScheduleSlots } from './modules/slot-generator';
import { initSendRequestModal, initAcceptInvitationModal } from './modules/modal-handlers';
import { initPasswordToggle, initModalReset } from './modules/password-toggle';

async function refreshHospitalDashboardRequests() {
    const tbody = document.getElementById('requests-table-body');
    if (!tbody || !window.hospitalAdminId) {
        return;
    }

    try {
        const response = await fetch(`${window.location.pathname}${window.location.search}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!response.ok) {
            return;
        }

        const html = await response.text();
        const parsedDocument = new DOMParser().parseFromString(html, 'text/html');
        const freshTbody = parsedDocument.getElementById('requests-table-body');

        if (freshTbody) {
            tbody.innerHTML = freshTbody.innerHTML;
        }
    } catch (error) {
        console.error('Error refreshing hospital dashboard requests:', error);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDonationScheduleSlots();
    initSendRequestModal();
    initAcceptInvitationModal();
    initPasswordToggle();
    initModalReset('addUserModal', 'addUserForm');
});

// Donation listeners: subscribe to hospital-scoped channels when on a hospital page
if (window.hospitalAdminId) {
    window.Echo.channel(`donations.${window.hospitalAdminId}`)
        .listen('.App\\Events\\DonationCreated', (e) => {
            console.log('New donation detected:', e.donation);
            window.dispatchEvent(new CustomEvent('donation-updated', { detail: e.donation }));

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
        })
        .listen('.App\\Events\\DonationStatusUpdated', (e) => {
            console.log('Donation status updated:', e.donation, e.fromStatus, e.toStatus);
            window.dispatchEvent(new CustomEvent('donation-updated', { detail: e.donation }));

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
        });
} else {
    // On non-hospital pages (donor pages), subscribe to a global donations channel if needed
    window.Echo.private('donations').listen('.App\\Events\\DonationCreated', (e) => {
        window.dispatchEvent(new CustomEvent('donation-updated', { detail: e.donation }));
    });
}

// Real-time listener for blood requests (hospital + users)
// Blood requests: subscribe to hospital-scoped channel when on hospital pages
if (window.hospitalAdminId) {
    window.Echo.channel(`blood-requests.${window.hospitalAdminId}`)
        .listen('.App\\Events\\BloodRequestCreated', (e) => {
            if (document.getElementById('requests-table-body')) {
                refreshHospitalDashboardRequests();
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
} else {
    // Non-hospital pages may still benefit from a global listener for notifications
    window.Echo.private('blood-requests').listen('.App\\Events\\BloodRequestCreated', (e) => {
        window.dispatchEvent(new CustomEvent('bloodrequest-updated', { detail: e.bloodRequest }));
    });
}

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

        if (document.getElementById('requests-table-body')) {
            await refreshHospitalDashboardRequests();
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

        if (document.getElementById('requests-table-body')) {
            await refreshHospitalDashboardRequests();
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

        if (document.getElementById('requests-table-body')) {
            await refreshHospitalDashboardRequests();
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

if (document.getElementById('requests-table-body') && window.hospitalAdminId) {
    setInterval(() => {
        refreshHospitalDashboardRequests();
    }, 30000);
}

// ===== Notification Bell Management =====
async function loadNotifications() {
    if (!window.hospitalAdminId) return;
    
    try {
        const response = await fetch('/hospital/notifications/unread');
        if (!response.ok) return;
        
        const data = await response.json();
        updateNotificationBadge(data.count);
        renderNotifications(data.notifications);
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (!badge) return;
    
    if (count > 0) {
        badge.textContent = count > 9 ? '9+' : count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function renderNotifications(notifications) {
    const container = document.getElementById('notificationsList');
    if (!container) return;
    
    if (notifications.length === 0) {
        container.innerHTML = '<li class="dropdown-item text-muted text-center py-3"><small>No notifications</small></li>';
        return;
    }
    
    const html = notifications.map(notif => {
        const createdAt = new Date(notif.created_at);
        const timeStr = createdAt.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        
        let message = 'Update received';
        let icon = 'bi-bell';
        
        if (notif.type === 'blood_request_created') {
            message = `New blood request for ${notif.data?.blood_type || 'blood'}`;
            icon = 'bi-droplet-fill';
        } else if (notif.type === 'blood_request_status_updated') {
            message = `Request status: ${notif.data?.from_status} → ${notif.data?.to_status}`;
            icon = 'bi-arrow-repeat';
        } else if (notif.type === 'donation_created') {
            message = `New donation scheduled at ${notif.data?.donation_time || 'scheduled time'}`;
            icon = 'bi-heart-pulse-fill';
        } else if (notif.type === 'donation_status_updated') {
            message = `Donation status: ${notif.data?.from_status} → ${notif.data?.to_status}`;
            icon = 'bi-arrow-repeat';
        }
        
        return `
            <li>
                <button class="dropdown-item small" onclick="markNotificationAsRead(${notif.id})">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-start gap-2 flex-grow-1">
                            <i class="bi ${icon} mt-1" style="flex-shrink: 0;"></i>
                            <div style="text-align: left; flex-grow: 1;">
                                <div class="fw-normal">${message}</div>
                                <small class="text-muted">${timeStr}</small>
                            </div>
                        </div>
                    </div>
                </button>
            </li>`;
    }).join('');
    
    container.innerHTML = html;
}

async function markNotificationAsRead(notificationId) {
    if (!window.hospitalAdminId) return;
    
    try {
        const response = await fetch(`/hospital/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        
        if (response.ok) {
            loadNotifications(); // Reload to update count and list
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

async function markAllNotificationsAsRead() {
    if (!window.hospitalAdminId) return;
    
    try {
        const response = await fetch('/hospital/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        
        if (response.ok) {
            loadNotifications();
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
}

// Make functions globally available
window.markNotificationAsRead = markNotificationAsRead;
window.markAllNotificationsAsRead = markAllNotificationsAsRead;

// Load notifications on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.hospitalAdminId) {
        loadNotifications();
        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    }
});

// Update notification bell on real-time events
window.addEventListener('bloodrequest-updated', () => {
    if (window.hospitalAdminId) {
        setTimeout(loadNotifications, 500); // Small delay to allow DB write
    }
});

window.addEventListener('bloodrequest-status-updated', () => {
    if (window.hospitalAdminId) {
        setTimeout(loadNotifications, 500);
    }
});

window.addEventListener('donation-updated', () => {
    if (window.hospitalAdminId) {
        setTimeout(loadNotifications, 500);
    }
});