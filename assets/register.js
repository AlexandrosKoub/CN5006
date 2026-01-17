document.getElementById('registerForm').onsubmit = function(e) {
        e.preventDefault();

        const btn = this.querySelector('.btn-submit');
        const responseDiv = document.getElementById('message-box'); // Add a div for messages

        btn.disabled = true;
        btn.innerText = 'Γίνεται επεξεργασία...';

        const formData = new FormData(this);

        fetch('CN5006/api_register.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if (data.status === 'success') {
                    responseDiv.innerHTML = `<p style='color:green; padding:10px; border:1px solid green;'>${data.message} <a href='login.php' style="color: #820202">Συνδεθείτε εδώ.</a></p>`;
                    this.reset(); // Clear the form
                } else {
                    responseDiv.innerHTML = `<p style='color:red; padding:10px; border:1px solid red;'>${data.message}</p>`;
                }
            })
            .catch(error => {
                responseDiv.innerHTML = `<p style='color:red;'>Σφάλμα επικοινωνίας με τον διακομιστή.</p>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Δημιουργία Λογαριασμού';
            });
    };