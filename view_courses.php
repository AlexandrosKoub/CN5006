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
    $sql = "SELECT c.title, c.course_id, c.description 
            FROM courses c 
            JOIN student_courses sc ON c.course_id = sc.course_id 
            JOIN students s ON sc.student_id = s.student_id
            WHERE s.user_id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Σφάλμα βάσης δεδομένων: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Τα Μαθήματά μου | Metropolitano</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; min-height: 100vh; margin: 0; background: #f4f4f4; }


        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .main-content { padding: 30px; }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .course-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border-top: 5px solid #820202;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }


        .course-title {
            margin: 10px 0;
            color: #820202;
            font-size: 1.25rem;
        }

        .btn-view {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #820202;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .btn-view:hover { background: #820202; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Metropolitano</h2>
    <p>Συνδεδεμένος ως Φοιτητής : <br><strong><?php echo $username; ?></strong></p>
    <ul>
        <li><a href="dashboard.php">Αρχική</a></li>
        <li class="active"><a href="view_courses.php">Τα Μαθήματά μου</a></li>
        <li><a href="view_assignment.php">Προβολή Εργασιών</a></li>
        <li><a href="submit_assignment.php">Κατάθεση Εργασιών</a></li>
        <li><a href="grades.php">Βαθμολογίες</a></li>
        <li><a href="logout.php">Αποσύνδεση</a></li>
    </ul>
</div>

<div class="main-wrapper">
    <div class="main-content">
        <h1>Τα Μαθήματά μου</h1>
        <p>Εδώ μπορείτε να δείτε το πρόγραμμα σπουδών σας και το υλικό των μαθημάτων.</p>

        <div class="course-grid">
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p style="color: #555; line-height: 1.5;">
                            <?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?>
                        </p>
                        <a href="<?php echo $course['course_id']; ?>" class="btn-view">Προβολή Εργασιών</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card" style="grid-column: 1/-1; padding: 40px; text-align: center;">
                    <h3>Δεν βρέθηκαν μαθήματα.</h3>
                    <p>Επικοινωνήστε με τη γραμματεία για την εγγραφή σας σε κάποιο απο τα μαθήματα.</p>
                </div>
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
