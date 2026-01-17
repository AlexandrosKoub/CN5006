<?php
header("Content-Type: application/json; charset=UTF-8");
include('includes/config.php');
session_start();

// rbac
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '1') {
    echo json_encode(["status" => "error", "message" => "Μη εξουσιοδοτημένη πρόσβαση."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment_file'])) {
    $student_id = $_SESSION['user_id'];
    $assignment_id = $_POST['assignment_id'];
    $target_dir = "uploads/";

    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_name = time() . "_" . basename($_FILES["assignment_file"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validations
    if ($_FILES["assignment_file"]["size"] > 5000000) {
        echo json_encode(["status" => "error", "message" => "Το αρχείο είναι πολύ μεγάλο (Max 5MB)."]);
    } elseif (!in_array($file_type, ['pdf', 'zip', 'rar'])) {
        echo json_encode(["status" => "error", "message" => "Μόνο PDF, ZIP, RAR επιτρέπονται."]);
    } else {
        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
            try {
                $sql = "INSERT INTO submissions (assignment_id, student_id, file_path) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$assignment_id, $student_id, $target_file])) {
                    echo json_encode(["status" => "success", "message" => "Η εργασία υποβλήθηκε επιτυχώς!"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Αποτυχία καταχώρησης στη βάση."]);
                }
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Σφάλμα SQL: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Αποτυχία ανεβάσματος αρχείου."]);
        }
    }
}
?>