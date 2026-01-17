<?php
include('includes/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role_id'];
$username = $_SESSION['username'];


?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolitano - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; margin: 0; background: #f4f4f4; }



    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως : <br><strong><?php echo $username; ?></strong></p>
    <ul>
        <li><a href="dashboard.php">Αρχική</a></li>

        <?php if($role == '1'): ?>
            <li><a href="view_courses.php">Τα Μαθήματά μου</a></li>
            <li><a href="view_assignment.php">Προβολή Εργασιών</a></li>
            <li><a href="submit_assignment.php">Κατάθεση Εργασιών</a></li>
            <li><a href="grades.php">Βαθμολογίες</a></li>
        <?php else: ?>
            <li><a href="manage_courses.php">Διαχείριση Μαθημάτων</a></li>
            <li><a href="view_submissions.php">Προβολή Υποβολών</a></li>
            <li><a href="post_assignment.php">Ανάρτηση Εργασιών</a></li>
            <li><a href="grade_students.php">Βαθμολογίες</a></li>
        <?php endif; ?>

        <li><a href="logout.php">Αποσύνδεση</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="welcome-header">
        <h1>Καλωσήρθατε, <?php echo $username; ?>!</h1>
        <p>Η σημερινή ημερομηνία είναι : <?php echo date('d/m/Y'); ?> .</p>
    </div>

    <div class="card-grid">
        <?php if($role == '1'): ?>
            <div class="card">
                <h3>Εκκρεμείς Εργασίες</h3>
                <p>Έχετε 3 εργασίες που πρέπει να υποβληθούν σύντομα.</p>
            </div>
            <div class="card">
                <h3>Μέσος Όρος</h3>
                <p>Ο τρέχων Μ.Ο. σας είναι: 6.7</p>
            </div>
        <?php else: ?>
            <div class="card">
                <h3>Σύνολο Μαθητών</h3>
                <p>Διδάσκετε σε 12 μαθητές.</p>
            </div>
            <div class="card">
                <h3>Νέες Υποβολές</h3>
                <p>Υπάρχουν 13 εργασίες προς βαθμολόγηση.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<footer class="footer-content">
    <div class="social-links">
        <a href="https://x.com/"><img src="images/twitter-fill.png" alt="Twitter"></a>
        <a href="https://www.instagram.com/"><img src="images/instagram-line.png" alt="Instagram"></a>
        <a href="https://www.facebook.com/"><img src="images/facebook-line.png" alt="Facebook"></a>
        <a href="https://www.tiktok.com/"><img src="images/tiktok-line.png" alt="TikTok"></a>
    </div>
    <p style="margin-top: 20px;"><b>Copyright &copy; 2026 <br> "Metropolitano Κολλέγιο"</b></p>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const welcomeHeader = document.querySelector('.welcome-header h1');
        const hour = new Date().getHours();
        let greeting = "";

        if (hour < 12) greeting = "Καλημέρα";
        else if (hour < 20) greeting = "Καλησπέρα";
        else greeting = "Καλό Βράδυ";

        const currentText = welcomeHeader.innerText;
        const name = currentText.split(',')[1];
        welcomeHeader.innerText = greeting + "," + name;
    });

    const logoutBtn = document.querySelector('a[href="logout.php"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm("Είστε σίγουροι ότι θέλετε να αποσυνδεθείτε;")) {
                e.preventDefault();
            }
        });
    }

    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = "translateY(-5px)";
            card.style.transition = "0.3s";
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = "translateY(0)";
        });
    });
</script>
</body>
</html>