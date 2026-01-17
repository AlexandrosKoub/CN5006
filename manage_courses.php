<?php
include('includes/config.php');
session_start();

//RBAC
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = "";

/* sql query to get all the courses where the teacher is assigned */
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    try {
        $del_stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ? AND teacher_id = ?");
        if ($del_stmt->execute([$id_to_delete, $teacher_id])) {
            $message = "<p class='alert success'>Το μάθημα διαγράφηκε επιτυχώς.</p>";
        }
    } catch (PDOException $e) {
        $message = "<p class='alert error'>Σφάλμα κατά τη διαγραφή: " . $e->getMessage() . "</p>";
    }
}

$stmt = $pdo->prepare("SELECT * FROM courses WHERE teacher_id = ?");
$stmt->execute([$teacher_id]);
$my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Μαθημάτων | Metropolitano</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background: #f4f4f4;
            flex-direction: row;
        }



        /* Main Content */

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        /* Table Design */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: 600; }

        /* Buttons */
        .btn { padding: 10px 18px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; transition: 0.3s; display: inline-block; cursor: pointer; border: none; }
        .btn-add { background: #820202; color: white; white-space: nowrap; }
        .btn-edit { background: #ffc107; color: #000; margin-bottom: 5px; }
        .btn-delete { background: #dc3545; color: white; }
        .action-cell { white-space: nowrap; }

        /* Alerts */
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }





        /* responsiveness */
        @media (max-width: 768px) {
            body { flex-direction: column; }

            .sidebar {
                width: 100%;
                height: 50%;
            }
            .main-content { padding: 25px 15px; }

            .header-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .btn-add { text-align: center; }
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr { border: 1px solid #ccc; margin-bottom: 15px; border-radius: 8px; background: #fff; padding: 5px; }
            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 45% !important;
                min-height: 40px;
                display: flex;
                align-items: center;
                flex-direction: row;
                flex-wrap: wrap;

            }
            td:last-child { border-bottom: 0; }

            td:before {
                position: absolute;
                left: 15px;
                width: 40%;
                font-weight: bold;
                content: attr(data-label);
                color: #820202;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως Καθηγητής :<br><strong><?php echo $_SESSION['username']; ?></strong></p>
    <ul>
        <li><a href="dashboard.php">Αρχική</a></li>
        <li><a href="manage_courses.php">Διαχείριση Μαθημάτων</a></li>
        <li><a href="view_submissions.php">Προβολή Υποβολών</a></li>
        <li><a href="post_assignment.php">Ανάρτηση Εργασιών</a></li>
        <li><a href="grade_students.php">Βαθμολογίες</a></li>
        <li><a href="logout.php">Αποσύνδεση</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header-actions">
        <h1>Διαχείριση Μαθημάτων</h1>
        <a href="add_course.php" class="btn btn-add">+ Νέο Μάθημα</a>
    </div>

    <?php echo $message; ?>

    <div class="table-container">
        <?php if (count($my_courses) > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>Τίτλος</th>
                    <th>Περιγραφή</th>
                    <th>Ενέργειες</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($my_courses as $course): ?>
                    <tr>
                        <td data-label="Τίτλος">
                            <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                        </td>
                        <td data-label="Περιγραφή">
                            <?php echo htmlspecialchars(substr($course['description'], 0, 60)) . '...'; ?>
                        </td>
                        <td data-label="Ενέργειες" class="action-cell">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a href="edit_course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-edit">Επεξεργασία</a>
                            <a href="manage_courses.php?delete_id=<?php echo $course['course_id']; ?>"
                               class="btn btn-delete"
                               onclick="return confirm('Είστε σίγουροι ότι θέλετε να διαγράψετε αυτό το μάθημα;');">Διαγραφή</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #666;">Δεν είστε υπεύθυνος για κάποιο μάθημα.</p>
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
