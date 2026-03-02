/**
 * Initialize sidebar toggle functionality
 */
export function initSidebar() {
    const toggler = document.getElementById('sidebarToggler');
    const sidebar = document.querySelector('.sidebar-modern') || document.querySelector('[id*="sidebar"]');
    const overlay = document.getElementById('sidebarOverlay');

    if (!toggler || !sidebar || !overlay) return;

    toggler.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
        
        if (sidebar.classList.contains('show')) {
            document.body.setAttribute('data-sidebar-open', 'true');
        } else {
            document.body.removeAttribute('data-sidebar-open');
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.removeAttribute('data-sidebar-open');
    });

    // Global function for backward compatibility - toggle directly rather than re-clicking
    window.toggleSidebar = function() {
        if (!toggler || !sidebar || !overlay) return;
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
        if (sidebar.classList.contains('show')) {
            document.body.setAttribute('data-sidebar-open', 'true');
        } else {
            document.body.removeAttribute('data-sidebar-open');
        }
    };
}