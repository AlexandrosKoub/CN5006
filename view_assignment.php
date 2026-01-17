<?php
include('includes/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role_id'];
$username = $_SESSION['username'] ;
$assignments = [];

try {
    if ($role == 1) {
        // SQL ΓΙΑ ΦΟΙΤΗΤΗ
        $sql = "SELECT a.assignment_id, a.title, a.description, a.deadline, c.title AS course_name 
                FROM assignments a
                JOIN courses c ON a.course_id = c.course_id
                JOIN student_courses sc ON c.course_id = sc.course_id
                JOIN students s ON sc.student_id = s.student_id
                WHERE s.user_id = ?
                ORDER BY a.deadline ASC";
    } else {
        // SQL ΓΙΑ ΚΑΘΗΓΗΤΗ
        $sql = "SELECT a.assignment_id, a.title, a.description, a.deadline, c.title AS course_name 
                FROM assignments a
                JOIN courses c ON a.course_id = c.course_id
                WHERE c.teacher_id = ?
                ORDER BY a.deadline ASC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εργασίες | Metropolitano</title>
    <link rel="stylesheet" href="style.css">
    <style>

        body { display: flex; min-height: 100vh; margin: 0; background: #f4f7f6; }

        /* Assignment Cards */
        .assignment-list { display: grid; gap: 20px; margin-top: 20px; }
        .assignment-card {
            background: white; border-radius: 12px; padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 6px solid #820202;
            display: flex; justify-content: space-between; align-items: center;
        }
        .info h3 { margin: 0 0 5px 0; color: #333; }
        .info p { margin: 0; color: #666; font-size: 0.9rem; }
        .badge {
            background: #eee; padding: 5px 12px; border-radius: 20px;
            font-size: 0.8rem; font-weight: bold; color: #820202;
        }
        .deadline { color: #d9534f; font-weight: bold; font-size: 0.85rem; }

        .btn-action {
            background: #820202; color: white; padding: 10px 18px;
            text-decoration: none; border-radius: 6px; font-size: 0.9rem;
        }
        .btn-action:hover { background: #9c0b0b; }

        @media (max-width: 768px) {
            body { display: flex; flex-direction: column;}
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #ddd; height: 50%; }
            .assignment-card { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως Φοιτητής : </br><strong><?php echo $username; ?></strong></p>
    <ul>
        <li><a href="dashboard.php">Αρχική</a></li>
        <li><a href="view_courses.php">Τα Μαθήματά μου</a></li>
        <li><a href="view_assignment.php">Προβολή Εργασιών</a></li>
        <li><a href="submit_assignment.php">Κατάθεση Εργασιών</a></li>
        <li><a href="grades.php">Βαθμολογίες</a></li>
        <li><a href="logout.php">Αποσύνδεση</a></li>
    </ul>
</div>

<div class="main-content">
    <h1><?php echo ($role == 1) ? "Οι Εργασίες μου" : "Διαχείριση Εργασιών"; ?></h1>
    <p>Δείτε τις εργασίες με τις προθεσμίες τους ανα μάθημα.</p>

    <div class="assignment-list">

        <?php if (count($assignments) > 0): ?>
            <?php foreach ($assignments as $task): ?>
                <div class="assignment-card">
                    <div class="info">
                        <span class="badge"><?php echo htmlspecialchars($task['course_name']); ?></span>
                        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($task['description'], 0, 80)) . "..."; ?></p>
                        <span class="deadline">Προθεσμία: <?php echo date("d/m/Y H:i", strtotime($task['deadline'])); ?></span>
                    </div>

                    <div class="actions">
                        <?php if ($role == 1): ?>
                            <a href="submit_assignment.php?id=<?php echo $task['assignment_id']; ?>" class="btn-action">Υποβολή</a>
                        <?php else: ?>
                            <a href="view_assignment.php?id=<?php echo $task['assignment_id']; ?>" class="btn-action">Προβολή Υποβολών</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="assignment-card" style="border-left-color: #ccc;">
                <p>Δεν υπάρχουν διαθέσιμες εργασίες αυτή τη στιγμή.</p>
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
    const logoutBtn = document.querySelector('a[href="logout.php"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm("Είστε σίγουροι ότι θέλετε να αποσυνδεθείτε;")) {
                e.preventDefault();
            }
        });
    }
</script>
</body>
</html>
