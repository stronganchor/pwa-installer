let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    const notification = document.getElementById('pwa-install-notification');
    const installBtn = document.getElementById('pwa-install-btn');

    notification.style.display = 'block';

    installBtn.addEventListener('click', () => {
        notification.style.display = 'none';
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(choiceResult => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
            } else {
                console.log('User dismissed the install prompt');
            }
            deferredPrompt = null;
        });
    });
});
