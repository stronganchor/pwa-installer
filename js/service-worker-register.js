(function () {
    // Avoid any work on /embed/ URLs
    if ((window.location.pathname || '').toLowerCase().includes('/embed/')) return;

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/?service-worker=1').catch(err => {
            console.error('Service Worker registration failed:', err);
        });
    }
})();
