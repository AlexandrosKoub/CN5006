// 1. LOGIN FORM
document.getElementById('loginForm').onsubmit = function(e) {
    e.preventDefault();

    const btn = this.querySelector('.btn-submit');
    const responseDiv = document.getElementById('message-box');

    btn.disabled = true;
    btn.innerText = 'Γίνεται έλεγχος...';

    const formData = new FormData(this);

    fetch('../api_login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                responseDiv.innerHTML = `<p style='color:green; font-weight:bold;'>${data.message} Γίνεται ανακατεύθυνση...</p>`;
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 1000);
            } else {
                responseDiv.innerHTML = `<p style='color:red; font-weight:bold;'>${data.message}</p>`;
                btn.disabled = false;
                btn.innerText = 'Είσοδος';
            }
        })
        .catch(error => {
            responseDiv.innerHTML = `<p style='color:red;'>Σφάλμα επικοινωνίας με τον διακομιστή.</p>`;
            btn.disabled = false;
            btn.innerText = 'Είσοδος';
        });
};

