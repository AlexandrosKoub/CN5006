const form = document.getElementById('uploadForm');
    const btn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');
    const responseDiv = document.getElementById('js-response');

    form.onsubmit = function(e) {
        e.preventDefault();

        btn.disabled = true;
        spinner.style.display = 'block';
        btnText.innerText = 'Γίνεται ανέβασμα...';
        responseDiv.innerHTML = "";

        const formData = new FormData(this);

        fetch('CN5006/api_submit_assignment.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    responseDiv.innerHTML = `<p style='color: #4bb543; font-weight: bold;'>${data.message}</p>`;

                    // --- ΝΕΟΣ ΚΩΔΙΚΑΣ: Αφαίρεση της επιλεγμένης εργασίας από τη λίστα ---
                    const selectElement = form.querySelector('select[name="assignment_id"]');
                    const selectedValue = selectElement.value;
                    const optionToRemove = selectElement.querySelector(`option[value="${selectedValue}"]`);

                    if (optionToRemove) {
                        optionToRemove.remove(); // Αφαιρεί την επιλογή από το DOM
                    }

                    form.reset(); // Καθαρίζει τα υπόλοιπα πεδία (π.χ. το αρχείο)
                } else {
                    responseDiv.innerHTML = `<p style='color: #ffcc00; font-weight: bold;'>${data.message}</p>`;
                }
            })
            .catch(error => {
                responseDiv.innerHTML = `<p style='color: red; font-weight: bold;'>Σφάλμα επικοινωνίας με τον διακομιστή.</p>`;
            })
            .finally(() => {
                btn.disabled = false;
                spinner.style.display = 'none';
                btnText.innerText = 'Υποβολή Εργασίας';
            });
    };
    const logoutBtn = document.querySelector('a[href="logout.php"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm("Είστε σίγουροι ότι θέλετε να αποσυνδεθείτε;")) {
                e.preventDefault();
            }
        });
    }