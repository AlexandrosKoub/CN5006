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
/*post method for the grade and update if already one */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_grade'])) {
    $sub_id = $_POST['submission_id'];
    $new_grade = $_POST['grade'];

    $update_sql = "UPDATE submissions SET grade = ? WHERE submission_id = ?";
    $stmt = $pdo->prepare($update_sql);
    if ($stmt->execute([$new_grade, $sub_id])) {
        $message = "<div class='alert success'>Ο βαθμός ενημερώθηκε επιτυχώς!</div>";
    }
}
/* sql query to show only submissions without grade and latest submission*/
try {
    $query = "SELECT s.submission_id, s.file_path, s.submission_date, s.grade, 
                 u.id AS student_id, u.username AS student_name, 
                 a.assignment_id, a.title AS assignment_title
          FROM submissions s
          JOIN users u ON s.student_id = u.id
          JOIN assignments a ON s.assignment_id = a.assignment_id
          JOIN courses c ON a.course_id = c.course_id
          WHERE c.teacher_id = ?
          AND s.grade IS NULL  
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Προβολή Εργασιών | Metropolitano</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            display: flex;
            background: #f4f4f4;
            margin: 0;
        }
        /* table with the data adjustments */
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
            width: 100%;
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
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px; }
        /* responsiveness*/
        @media (max-width: 768px) {
            body { flex-direction: column; }

            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #ddd; height: 50%; }

            /* table adjustment for responsiveness*/
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            tr {
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 8px;
                background: #fff;
            }
            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50% !important; /* using important to seperate the items as nothing else worked*/
                text-align: right !important;
            }
            td:last-child { border-bottom: 0; }
            /* Add labels so that you can still see them at table */
            td:before {
                position: absolute;
                left: 15px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
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
    <div class="header-section" >
        <h1 style="margin: 0;">Προβολή Υποβολών</h1>
        <div class="current-date">Η σημερινή ημερομηνία είναι: <?php echo date("d/m/Y"); ?></div>
    </div>


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
                    <td data-label="Φοιτητής"><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                    <td data-label="Εργασία"><?= htmlspecialchars($row['assignment_title']) ?></td>
                    <td data-label="Ημερομηνία"><?= date("d/m/Y H:i", strtotime($row['submission_date'])) ?></td>
                    <td data-label="Αρχείο"><a href="<?= $row['file_path'] ?>" target="_blank">Άνοιγμα Αρχείου</a></td>
                    <form method="POST">
                        <td data-label="Βαθμός">
                            <input type="hidden" name="submission_id" value="<?= $row['submission_id'] ?>">
                            <input type="number" name="grade" step="0.1" min="0" max="10"
                                   value="<?= $row['grade'] ?>" class="grade-input">
                        </td>
                        <td data-label="Ενέργειες">
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
<script src="assets/main.js"></script>
</body>
</html>
