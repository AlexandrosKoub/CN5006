document.addEventListener('DOMContentLoaded', () => {
    const uploadForm = document.getElementById('uploadForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');
    const responseDiv = document.getElementById('js-response');

    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        submitBtn.disabled = true;
        btnText.innerText = "Παρακαλώ περιμένετε...";
        spinner.style.display = "block";
        responseDiv.innerHTML = "";

        const formData = new FormData(uploadForm);

        try {
            const response = await fetch('../api_submit_assignment.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                responseDiv.innerHTML = `
                    <div style="padding:15px; background:#d4edda; color:#155724; border-radius:8px; margin-bottom:20px;">
                        ${result.message}
                    </div>`;
                uploadForm.reset(); // Clear the form

                setTimeout(() => { window.location.reload(); }, 2000);

            } else {
                throw new Error(result.message || "Σφάλμα κατά την υποβολή.");
            }

        } catch (error) {
            responseDiv.innerHTML = `
                <div style="padding:15px; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:20px;">
                    Σφάλμα: ${error.message}
                </div>`;
        } finally {
            submitBtn.disabled = false;
            btnText.innerText = "Υποβολή Εργασίας";
            spinner.style.display = "none";
        }
    });
});