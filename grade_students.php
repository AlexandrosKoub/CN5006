<?php
include('includes/config.php');
session_start();

//rbac
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = "";
$selected_course = isset($_GET['course_id']) ? $_GET['course_id'] : null;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_grade'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade_val = $_POST['grade_val'];
    $period = $_POST['exam_period'];

    try {
        /* update if already assigned */
        $sql = "INSERT INTO grades (student_id, course_id, grade, exam_period) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE grade = VALUES(grade), exam_period = VALUES(exam_period)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$student_id, $course_id, $grade_val, $period])) {
            $message = "<div class='alert success'>Η βαθμολογία καταχωρήθηκε επιτυχώς!</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert error'>Σφάλμα: " . $e->getMessage() . "</div>";
    }
}

/* all courses sql code */
$courses = $pdo->prepare("SELECT course_id, title FROM courses WHERE teacher_id = ?");
$courses->execute([$teacher_id]);
$my_courses = $courses->fetchAll(PDO::FETCH_ASSOC);

/* students per course sql code */
$students_enrolled = [];
if ($selected_course) {
    $sql_students = "SELECT s.student_id, u.username, g.grade, g.exam_period 
                     FROM students s
                     JOIN users u ON s.user_id = u.id
                     JOIN student_courses sc ON s.student_id = sc.student_id
                     LEFT JOIN grades g ON g.grade_id = (
                         SELECT MAX(grade_id) 
                         FROM grades 
                         WHERE student_id = s.student_id AND course_id = sc.course_id
                     )
                     WHERE sc.course_id = ?";

    $stmt_st = $pdo->prepare($sql_students);
    $stmt_st->execute([$selected_course]);
    $students_enrolled = $stmt_st->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Βαθμολόγηση | Metropolitano</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; min-height: 100vh; margin: 0; background: #f4f4f4; }

        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 30px; display: flex; flex-direction: column; align-items: center; }
        /* Container */
        .content-card {
            background: white; padding: 30px; border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 900px;
        }
        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f8f8f8; }
        /* grade input and save*/
        .grade-input { width: 70px; padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        .btn-save { background: #820202; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; }

        .alert { width: 100%; max-width: 900px; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: 50%; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως Καθηγητής : <br><strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
    <ul>
        <li><a href="dashboard.php">Αρχική</a></li>
        <li><a href="manage_courses.php">Διαχείριση Μαθημάτων</a></li>
        <li><a href="view_submissions.php">Προβολή Υποβολών</a></li>
        <li><a href="post_assignment.php">Ανάρτηση Εργασιών</a></li>
        <li><a href="grade_students.php">Βαθμολογίες</a></li>
        <li><a href="logout.php">Αποσύνδεση</a></li>
    </ul>
</div>

<div class="main-wrapper">
    <div class="main-content">
        <h1>Βαθμολόγηση Φοιτητών</h1>

        <?php echo $message; ?>

        <div class="content-card">
            <form method="GET" style="margin-bottom: 30px;">
                <label>Επιλέξτε Μάθημα:</label>
                <select name="course_id" onchange="this.form.submit()" style="padding: 10px; width: 100%; margin-top: 10px;">
                    <option value="">-- Επιλογή --</option>
                    <?php foreach ($my_courses as $c): ?>
                        <option value="<?php echo $c['course_id']; ?>" <?php echo ($selected_course == $c['course_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($selected_course && count($students_enrolled) > 0): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Φοιτητής</th>
                        <th>Περίοδος</th>
                        <th>Βαθμός (0-10)</th>
                        <th>Ενέργεια</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students_enrolled as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                            <form method="POST">
                                <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                <input type="hidden" name="course_id" value="<?php echo $selected_course; ?>">
                                <td>
                                    <input type="text" name="exam_period" value="<?php echo $student['exam_period'] ?? 'January 2026'; ?>" class="grade-input" style="width: 120px;">
                                </td>
                                <td>
                                    <input type="number" name="grade_val" step="0.1" min="0" max="10" value="<?php echo $student['grade']; ?>" class="grade-input">
                                </td>
                                <td>
                                    <button type="submit" name="submit_grade" class="btn-save">Αποθήκευση</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($selected_course): ?>
                <p>Δεν βρέθηκαν εγγεγραμμένοι φοιτητές σε αυτό το μάθημα.</p>
            <?php endif; ?>
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
