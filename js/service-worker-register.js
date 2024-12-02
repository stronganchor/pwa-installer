if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('?service-worker=1')
        .then(reg => console.log('Service Worker registered:', reg))
        .catch(err => console.error('Service Worker registration failed:', err));
}
