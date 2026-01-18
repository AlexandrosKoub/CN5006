<?php
session_start();
require 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή | Metropolitano</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>
        .container {
            max-width: 450px;
            width: 90%;
            margin: 60px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        h2 {
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid ;
            border-radius: 10px;
            font-size: 1rem;
            box-sizing: border-box;
            background-color: #dbe0e4;
        }
        input:focus, select:focus {
            outline: none;
            background-color: #dbe0e4;
            box-shadow: 0 0 0 4px;
        }
        .btn-submit {
            width: 100%;
            background: #8e8b8c;
            color: #f6f2f2;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #9c0b0b;
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



        /* Responsive */
        @media (max-width: 480px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            .container {
                padding: 10px;
                margin: 10px auto;
                width: 90%;
            }
            h2 {
                font-size: 1.5rem;
            }
            input, select, .btn-submit {
                font-size: 16px;
            }
        }


    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">
        Metropolitano
        <img src="/images/4.png" style="height: 30px;" alt="Logo">
    </div>
    <nav>
        <ul>
            <li><a href="main_page.php">Αρχική</a></li>
            <li><a href="login.php">Σύνδεση</a></li>
            <li><a href="register.php">Εγγραφή</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <div id="message-box"></div><h2>Φόρμα Εγγραφής</h2>

    <form id="registerForm" method="POST" autocomplete="off">
        <div class="form-group">
            <label>Όνομα Χρήστη (Username)</label>
            <input type="text" name="username" placeholder="Academic Username" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="example@mail.com" required>
        </div>

        <div class="form-group">
            <label>Κωδικός Πρόσβασης</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>Επιλογή Ρόλου</label>
            <select name="role" required>
                <option value="" disabled selected>Επιλέξτε ιδιότητα...</option>
                <option value="1">Φοιτητής</option>
                <option value="2">Καθηγητής</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ειδικός Κωδικός Εγγραφής</label>
            <input type="text" name="reg_code" required placeholder="CAPS ON">
        </div>

        <button type="submit" class="btn-submit">Δημιουργία Λογαριασμού</button>
    </form>

    <p class="login-link">Έχετε ήδη λογαριασμό; <a href="login.php">Συνδεθείτε</a></p>
</div>

<footer class="footer-content">
    <div class="social-links">
        <a href="https://x.com/"><img src="/images/twitter-fill.png" alt="Twitter"></a>
        <a href="https://www.instagram.com/"><img src="/images/instagram-line.png" alt="Instagram"></a>
        <a href="https://www.facebook.com/"><img src="/images/facebook-line.png" alt="Facebook"></a>
        <a href="https://www.tiktok.com/"><img src="/images/tiktok-line.png" alt="TikTok"></a>
    </div>
    <p style="margin-top: 20px;"><b>Copyright &copy; 2026 <br> "Metropolitano Κολλέγιο"</b></p>
</footer>
<script src="assets/main.js"></script>
<script src="assets/register.js"></script>
</body>
</html>
