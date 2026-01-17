<?php
include('includes/config.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];


try {
    $sql = "SELECT c.title, c.description, g.grade, g.exam_period, g.updated_at 
            FROM grades g
            JOIN courses c ON g.course_id = c.course_id
            JOIN students s ON g.student_id = s.student_id
            WHERE s.user_id = ? 
            AND g.updated_at = (
                SELECT MAX(updated_at) 
                FROM grades g2 
                WHERE g2.course_id = g.course_id 
                AND g2.student_id = g.student_id
            )
            ORDER BY c.title ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    $course_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Σφάλμα συστήματος: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Οι Βαθμολογίες μου | Metropolitano</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { display: flex; min-height: 100vh; margin: 0;  background: #f8f9fa; }

        .main-content { flex-grow: 1; padding: 40px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        .grades-table-container { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 15px; border-bottom: 2px solid #eee; color: #555; font-weight: 600; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }

        .grade-badge { padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; display: inline-block; }
        .pass { background: #d4edda; color: #155724; }
        .fail { background: #f8d7da; color: #721c24; }

        .date-text { font-size: 0.8rem; color: #888; }
        @media (max-width: 768px) {
            body { display: flex; flex-direction: column;}
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #ddd; height: 50%; }
            table, thead, tbody, th, td, tr { display: block; }

            /* Hide the header labels (but keep for accessibility) */
            thead tr { position: absolute; top: -9999px; left: -9999px; }

            tr {
                background: #fff; border: 1px solid #eee; border-radius: 12px;
                margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                padding: 10px;
            }

            td {
                border: none; border-bottom: 1px solid #f9f9f9; position: relative;
                padding-left: 50% !important; text-align: right !important;
                min-height: 40px; display: flex; align-items: center; justify-content: flex-end;
            }

            td:last-child { border-bottom: none; }

            /* Use data-label to insert headings on the left of each row */
            td:before {
                content: attr(data-label);
                position: absolute; left: 15px; width: 45%; padding-right: 10px;
                white-space: nowrap; text-align: left; font-weight: bold; color: #820202;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <div ">
        Συνδεδεμένος ως Φοιτητής : <br><strong><?php echo htmlspecialchars($username); ?></strong>
    </div>
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
    <div class="header-section">
        <h1>Οι Βαθμολογίες μου</h1>
        <div class="current-date">Η σημερινή ημερομηνία είναι : <?php echo date("d/m/Y"); ?></div>
    </div>

    <div class="grades-table-container">
        <?php if (count($course_grades) > 0) : ?>
            <table>
                <thead>
                <tr>
                    <th>Κωδικός Μαθήματος</th>
                    <th>Μάθημα</th>
                    <th>Τελευταία Εξεταστική</th>
                    <th>Βαθμός</th>
                    <th>Ημερομηνία Καταχώρησης</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($course_grades as $row): ?>
                    <tr>
                        <td data-label="Μάθημα"><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td data-label="Περιγραφή"><code><?php echo htmlspecialchars($row['description']); ?></code></td>
                        <td data-label="Εξεταστική"><?php echo htmlspecialchars($row['exam_period']); ?></td>
                        <td data-label="Βαθμός">
                            <span class="grade-badge <?php echo ($row['grade'] >= 5) ? 'pass' : 'fail'; ?>">
                                <?php echo number_format($row['grade'], 2); ?>
                            </span>
                        </td>
                        <td data-label="Ημερομηνία" class="date-text">
                            <?php echo date("d M Y", strtotime($row['updated_at'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <p>Δεν βρέθηκαν βαθμολογίες για τα μαθήματά σας.</p>
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
