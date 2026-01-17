<?php
include('includes/config.php');
session_start();

// rbac
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$message = "";

try {
    $stmt_courses = $pdo->prepare("SELECT course_id, title FROM courses WHERE teacher_id = ?");
    $stmt_courses->execute([$teacher_id]);
    $my_courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Σφάλμα ανάκτησης μαθημάτων: " . $e->getMessage());
}

//sql
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    if (!empty($course_id) && !empty($title) && !empty($deadline)) {
        try {

            $sql = "INSERT INTO assignments (course_id, title, description, deadline) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$course_id, $title, $description, $deadline])) {
                $message = "<div class='alert success'>Η εργασία αναρτήθηκε επιτυχώς!</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert error'>Σφάλμα κατά την ανάρτηση: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert error'>Παρακαλώ συμπληρώστε όλα τα υποχρεωτικά πεδία.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ανάρτηση Εργασίας | Metropolitano</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>

        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background: #f4f4f4;
            flex-direction: row;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .header-text { text-align: center; margin-bottom: 30px; }
        .header-text h1 { color: #820202; margin: 0; }


        .form-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
        }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        select, input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        textarea { height: 120px; resize: none; }

        .btn-post {
            background: #820202;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            width: 100%;
            transition: background 0.3s;
        }
        .btn-post:hover { background: #5a0101; }

        /*RESPONSIVE*/
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%;height: 50%; }
            .form-container { padding: 25px; }
            .main-content { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως Καθηγητής : <br> <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
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
        <div class="header-text">
            <h1>Ανάρτηση Εργασίας</h1>
            <p>Παρακαλώ εισάγετε πληροφορίες για την εργασία.</p>
        </div>

        <?php echo $message; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label>Επιλογή Μαθήματος *</label>
                    <select name="course_id" required>
                        <option value="">-- Επιλέξτε --</option>
                        <?php foreach ($my_courses as $course): ?>
                            <option value="<?php echo $course['course_id']; ?>">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Τίτλος Εργασίας *</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Προθεσμία Υποβολής *</label>
                    <input type="datetime-local" name="deadline" required>
                </div>

                <div class="form-group">
                    <label>Περιγραφή</label>
                    <textarea name="description"></textarea>
                </div>

                <button type="submit" class="btn-post">Δημοσίευση</button>
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
</body>
</html>
