import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global Toast event handler
window.showToast = (message, type = 'success') => {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: { message, type }
    }));
};

Alpine.start();
