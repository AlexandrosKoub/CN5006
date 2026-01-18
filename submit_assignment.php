<?php
include('includes/config.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '1') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

try {
    // sql για να μην εμφανιζονται υποβολες που εχουν κατατεθει
    $sql = "SELECT a.assignment_id, a.title as assignment_title, c.title as course_title 
            FROM assignments a
            JOIN courses c ON a.course_id = c.course_id
            LEFT JOIN submissions s ON a.assignment_id = s.assignment_id AND s.student_id = ?
            WHERE s.submission_id IS NULL
            ORDER BY c.title ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $available_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Σφάλμα συστήματος: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Κατάθεση Εργασίας - Metropolitano</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { display: flex; min-height: 100vh; margin: 0; background: #f4f4f4; }



        /* Spinner & Button */
        .btn-submit {
            width: 100%; background: #820202; color: white; padding: 14px;
            border: none; border-radius: 10px; font-weight: 600; cursor: pointer;
            display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .spinner { display: none; width: 18px; height: 18px; border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%; border-top-color: #fff; animation: spin 1s infinite; }
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: white;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Responsive */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; box-sizing: border-box; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως Φοιτητής : <br><strong><?php echo $username; ?></strong></p>
    <ul>
        <li><a href="dashboard.php">Αρχική</a></li>
        <li><a href="view_courses.php">Τα Μαθήματά μου</a></li>
        <li><a href="view_assignment.php">Προβολή Εργασιών</a></li>
        <li><a href="submit_assignment.php">Κατάθεση Εργασιών</a></li>
        <li><a href="grades.php">Βαθμολογίες</a></li>
        <li><a href="logout.php">Αποσύνδεση</a></li>
    </ul>
</div>

<div class="main-wrapper">
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: baseline;">
            <h1>Κατάθεση Εργασιών</h1>
            <div class="current-date">Η σημερινή ημερομηνία είναι : <?php echo date("d/m/Y"); ?></div>
        </div>

        <div class="submission-card">
            <h2 style="margin-top:0;">Κατάθεση Εργασίας</h2>
            <p style="color: #666;">Ανεβάστε το αρχείο σας με το κωδικό του μαθήματος στο όνομα. (πχ. CN5005.ΑριθμοςΜητρωου.pdf)</p>

            <div id="js-response"></div>

            <form id="uploadForm" action="submit_assignment.php" method="post" enctype="multipart/form-data">

                <div style="margin: 25px 0;">
                    <label style="display:block; margin-bottom:10px; font-weight:bold;">Επιλογή Μαθήματος / Εργασίας:</label>
                    <select name="assignment_id" required>
                        <option value="" disabled selected>Επιλέξτε την εργασία σας...</option>
                        <?php foreach ($available_assignments as $task): ?>
                            <option value="<?= $task['assignment_id'] ?>">
                                <?= htmlspecialchars($task['course_title']) ?> - <?= htmlspecialchars($task['assignment_title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin: 25px 0;">
                    <label style="display:block; margin-bottom:10px; font-weight:bold;">Επιλογή Αρχείου (PDF/ZIP):</label>
                    <input type="file" name="assignment_file" required style="width:100%;">
                </div>

                <button type="submit" id="submitBtn" class="btn-submit">
                    <span id="btnText">Υποβολή Εργασίας</span>
                    <div id="spinner" class="spinner"></div>
                </button>
            </form>
        </div>
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
<script src="assets/main.js"></script>
<script src="assets/submit.js"></script>

</body>
</html>