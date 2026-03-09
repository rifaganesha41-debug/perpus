// assets/js/main.js

/**
 * Live search function using AJAX
 * @param {string} inputId - ID of search input
 * @param {string} targetId - ID of table tbody or container to update
 * @param {string} url - endpoint URL
 */
function liveSearch(inputId, targetId, url) {
    const input = document.getElementById(inputId);
    const target = document.getElementById(targetId);

    if (!input || !target) return;

    input.addEventListener('keyup', function() {
        const query = this.value;
        const xhr = new XMLHttpRequest();
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                target.innerHTML = xhr.responseText;
            }
        };
        
        xhr.open('GET', url + '?q=' + encodeURIComponent(query), true);
        xhr.send();
    });
}

// Auto-dismiss alerts
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000);
    });
});
