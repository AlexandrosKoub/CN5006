<?php
include('includes/config.php');
session_start();

/*RBAC*/
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$assignment_filter = isset($_GET['id']) ? $_GET['id'] : null;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_grade'])) {
    $sub_id = $_POST['submission_id'];
    $new_grade = $_POST['grade'];

    $update_sql = "UPDATE submissions SET grade = ? WHERE submission_id = ?";
    $stmt = $pdo->prepare($update_sql);
    if ($stmt->execute([$new_grade, $sub_id])) {
        $message = "<div class='alert success'>Ο βαθμός ενημερώθηκε επιτυχώς!</div>";
    }
}

try {
    $query = "SELECT s.submission_id, s.file_path, s.submission_date, s.grade, 
                 u.id AS student_id, u.username AS student_name, 
                 a.assignment_id, a.title AS assignment_title
          FROM submissions s
          JOIN users u ON s.student_id = u.id
          JOIN assignments a ON s.assignment_id = a.assignment_id
          JOIN courses c ON a.course_id = c.course_id
          WHERE c.teacher_id = ?
          AND s.grade IS NULL  /* <--- Add this line */
          AND s.submission_id = (
              SELECT MAX(s2.submission_id)
              FROM submissions s2
              WHERE s2.student_id = s.student_id
              AND s2.assignment_id = s.assignment_id
          )";

    $params = [$teacher_id];

    if ($assignment_filter) {
        $query .= " AND a.assignment_id = ?";
        $params[] = $assignment_filter;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Σφάλμα SQL: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Προβολή Εργασιών | Metropolitano</title>
    <link rel="stylesheet" href="style.css">
    <style>

        body {
            display: flex;
            background: #f4f4f4;
            margin: 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .data-table th, .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .data-table th {
            background: #eee;
        }

        .grade-input {
            width: 60px;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .btn-update {
            background: #820202;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως Καθηγητής :<br><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
    <ul style="list-style: none; padding: 0;">
        <li><a href="dashboard.php">Αρχική</a></li>
        <li><a href="manage_courses.php">Διαχείριση Μαθημάτων</a></li>
        <li><a href="view_submissions.php">Προβολή Υποβολών</a></li>
        <li><a href="post_assignment.php">Ανάρτηση Εργασιών</a></li>
        <li><a href="grade_students.php">Βαθμολογίες</a></li>
        <li><a href="logout.php">Αποσύνδεση</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Προβολή Υποβολών</h1>
    <?= $message ?>

    <table class="data-table">
        <thead>
        <tr>
            <th>Φοιτητής</th>
            <th>Εργασία</th>
            <th>Ημερομηνία</th>
            <th>Αρχείο</th>
            <th>Βαθμός</th>
            <th>Ενέργεια</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($submissions) > 0): ?>
            <?php foreach ($submissions as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                    <td><?= htmlspecialchars($row['assignment_title']) ?></td>
                    <td><?= date("d/m/Y H:i", strtotime($row['submission_date'])) ?></td>
                    <td><a href="<?= $row['file_path'] ?>" target="_blank">Άνοιγμα Αρχείου</a></td>
                    <form method="POST">
                        <td>
                            <input type="hidden" name="submission_id" value="<?= $row['submission_id'] ?>">
                            <input type="number" name="grade" step="0.1" min="0" max="10"
                                   value="<?= $row['grade'] ?>" class="grade-input">
                        </td>
                        <td>
                            <button type="submit" name="update_grade" class="btn-update">Αποθήκευση</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">Δεν βρέθηκαν υποβολές.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
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
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(response => {
            if(response.ok) {
                // 1. Give visual feedback
                statusMsg.innerHTML = '✅ Βαθμολογήθηκε!';
                row.style.backgroundColor = '#e8f5e9';

                // 2. Fade out and remove the row
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(20px)';

                    setTimeout(() => {
                        row.remove(); // Removes the row from the table completely

                        // Optional: Show message if table is now empty
                        const tbody = document.getElementById('submissionsTable');
                        if (tbody.querySelectorAll('tr').length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">🎉 Όλα τα μαθήματα βαθμολογήθηκαν!</td></tr>';
                        }
                    }, 500);
                }, 1000);
            }
        })
    }
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