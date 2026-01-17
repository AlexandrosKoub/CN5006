<?php
require "includes/config.php";
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σύνδεση | Metropolitano</title>
    <link rel="stylesheet" href="style.css">
    <style>

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 60px 60px 60px 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }
        h2 {
            margin-bottom: 25px;
            text-align: center;
            color: #0e0e0e;
            font-size: 1.5rem;
        }
        .form-group { margin-bottom: 1.2rem; }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #555;
        }

        input {
            width: 100%;
            padding: 14px ;
            border: 1px solid #eee;
            border-radius: 10px;
            font-size: 1rem;
            background-color: #f8f9fa;
        }
        input:focus {
            outline: none;
            border-color: #820202;
            background-color: #dbe0e4;
            box-shadow: 0 0 0 3px rgba(130, 2, 2, 0.15);
        }
        .btn-submit {
            width: 100%;
            background: #820202;
            color: #ffffff;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: #5a0101;
            transform: translateY(-1px);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #4e4949;
        }

        .login-link a {
            color: #571d1d;
            text-decoration: none;
            font-weight: 600;
        }
        @media (min-width:321px and max-width: 425px) {
            .container {
                width: 130px;
                padding: 20px 30px 20px 20px;
            }
        }
        @media (max-width: 320px) {
            .navbar {
                flex-direction: column;
                padding: 15px;
                text-align: center;
            }
            .container {
                width: 90%;
                padding: 20px 40px 20px 20px;
            }
            nav ul {
                margin-top: 10px;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">
        Metropolitano
        <img src="images/4.png" style="height: 30px; width: auto;" alt="Logo">
    </div>

    <nav>
        <ul>
            <li><a href="main_page.php">Αρχική</a></li>
            <li><a href="login.php">Σύνδεση</a></li>
            <li><a href="register.php">Εγγραφή</a></li>
        </ul>
    </nav>
</header>

<main>
    <div class="container">
        <div id="message-box"></div>
        <h2>Σύνδεση Χρήστη</h2>

        <form id="loginForm">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="example@mail.com" required>
            </div>

            <div class="form-group">
                <label>Κωδικός Πρόσβασης</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">Είσοδος</button>
        </form>
        <p class="login-link">Δεν έχετε λογαριασμό; <a href="register.php">Εγγραφείτε εδώ</a></p>
    </div>
</main>

<footer class="footer-content">
    <div class="social-links">
        <a href="https://x.com/"><img src="images/twitter-fill.png" alt="Twitter"></a>
        <a href="https://www.instagram.com/"><img src="images/instagram-line.png" alt="Instagram"></a>
        <a href="https://www.facebook.com/"><img src="images/facebook-line.png" alt="Facebook"></a>
        <a href="https://www.tiktok.com/"><img src="images/tiktok-line.png" alt="TikTok"></a>
    </div>
    <p><b>Copyright &copy; 2026 <br> "Metropolitano Κολλέγιο"</b></p>
</footer>
<script>
    document.getElementById('loginForm').onsubmit = function(e) {
        e.preventDefault();

        const btn = this.querySelector('.btn-submit');
        const responseDiv = document.getElementById('message-box');

        btn.disabled = true;
        btn.innerText = 'Γίνεται έλεγχος...';

        const formData = new FormData(this);

        fetch('api_login.php', {
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


    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
</body>
</html>