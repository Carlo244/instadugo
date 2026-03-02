/**
 * Initialize password visibility toggle
 */
export function initPasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
}

/**
 * Reset modal form when closed
 */
export function initModalReset(modalId, formId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.addEventListener('hidden.bs.modal', function() {
        const form = document.getElementById(formId);
        if (form) form.reset();
    });
}
