import './bootstrap';
document.addEventListener('livewire:load', function () {
    Livewire.on('open-new-tab', url => {
        if (url) {
            window.open(url, '_blank'); // buka tab baru
        }
    });
});
