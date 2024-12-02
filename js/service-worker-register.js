if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/?service-worker=1').catch(err => {
        console.error('Service Worker registration failed:', err);
    });
}
