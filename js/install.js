let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    const notification = document.getElementById('pwa-install-notification');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');

    notification.style.display = 'block';

    installBtn.textContent = pwaInstallVars.installText;
    dismissBtn.textContent = pwaInstallVars.dismissText;

    installBtn.addEventListener('click', () => {
        notification.style.display = 'none';
        deferredPrompt.prompt();

        deferredPrompt.userChoice.then(choiceResult => {
            if (choiceResult.outcome === 'accepted') {
                console.log('PWA installed');
            }
            deferredPrompt = null;
        }).catch(err => {
            console.error('Error handling install prompt:', err);
        });
    });

    dismissBtn.addEventListener('click', () => {
        notification.style.display = 'none';
    });
});
