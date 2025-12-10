<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.addEventListener('open-new-tab', event => {
            const url = event.detail.url;
            if (url) {
                window.open(url, '_blank');
            }
        });
    });
</script>
