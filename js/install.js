let deferredPrompt;

function isStandalone() {
    // iOS Safari uses navigator.standalone; others use the display-mode media query
    return (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) ||
           (typeof window.navigator.standalone !== 'undefined' && window.navigator.standalone === true);
}

function isEmbedPath() {
    return (window.location.pathname || '').toLowerCase().includes('/embed/');
}

function isLikelyPWASupported() {
    // Basic, practical gating: needs service workers, an install prompt API, and not already installed
    const supportsSW = 'serviceWorker' in navigator;
    const hasInstallPromptAPI = ('BeforeInstallPromptEvent' in window) || ('onbeforeinstallprompt' in window);
    return supportsSW && hasInstallPromptAPI && !isStandalone();
}

window.addEventListener('beforeinstallprompt', (e) => {
    // Never show on /embed/ URLs and only show when device is likely to support install
    if (isEmbedPath() || !isLikelyPWASupported()) return;

    e.preventDefault();
    deferredPrompt = e;

    const notification = document.getElementById('pwa-install-notification');
    const installBtn   = document.getElementById('pwa-install-btn');
    const dismissBtn   = document.getElementById('pwa-dismiss-btn');

    if (!notification || !installBtn || !dismissBtn) return;

    // Localized button text (provided via wp_localize_script)
    if (typeof pwaInstallVars !== 'undefined') {
        installBtn.textContent = pwaInstallVars.installText || 'Install App';
        dismissBtn.textContent = pwaInstallVars.dismissText || 'Dismiss';
    }

    notification.style.display = 'block';

    installBtn.addEventListener('click', () => {
        notification.style.display = 'none';
        if (!deferredPrompt || typeof deferredPrompt.prompt !== 'function') return;

        deferredPrompt.prompt();
        deferredPrompt.userChoice
            .then((choiceResult) => {
                // Optional: could persist acceptance/dismissal signal if desired
                // e.g., localStorage.setItem('pwaInstallChoice', choiceResult.outcome);
                deferredPrompt = null;
            })
            .catch(() => { deferredPrompt = null; });
    });

    dismissBtn.addEventListener('click', () => {
        notification.style.display = 'none';
        // Optional: remember dismissal to avoid showing again soon
        // localStorage.setItem('pwaInstallDismissed', Date.now().toString());
    });
});
