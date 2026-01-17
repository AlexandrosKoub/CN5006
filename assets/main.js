document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Διόρθωση Corruption (Back/Forward Cache Fix)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });

    // 2. Επιβεβαίωση Αποσύνδεσης
    const logoutBtn = document.querySelector('a[href="logout.php"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm("Είστε σίγουροι ότι θέλετε να αποσυνδεθείτε;")) {
                e.preventDefault();
            }
        });
    }
});