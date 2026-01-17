<?php
header('Content-Type: application/json');
require 'includes/config.php';

$response = ["status" => "error", "message" => "Κάτι πήγε στραβά."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role_id = $_POST['role'] ?? '';
    $reg_code = $_POST['reg_code'] ?? '';

    // 1. Validation Logic
    $is_valid_code = false;
    if ($role_id == "1" && $reg_code === "STUD2025") {
        $is_valid_code = true;
    } elseif ($role_id == "2" && $reg_code === "PROF2025") {
        $is_valid_code = true;
    }

    if (!$is_valid_code) {
        $response["message"] = "Λανθασμένος κωδικός εγγραφής για τον ρόλο αυτό.";
    } else {
        try {
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkEmail->execute([$email]);

            if ($checkEmail->rowCount() > 0) {
                $response["message"] = "Το email χρησιμοποιείται ήδη.";
            } else {
                $pdo->beginTransaction();

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt1 = $pdo->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
                $stmt1->execute([$username, $email, $hashed_password, $role_id]);
                $new_user_id = $pdo->lastInsertId();

                if ($role_id == "1") {
                    $stmt2 = $pdo->prepare("INSERT INTO students (email, user_id) VALUES (?, ?)");
                    $stmt2->execute([$email, $new_user_id]);
                } else {
                    $stmt2 = $pdo->prepare("INSERT INTO teachers (email, user_id) VALUES (?, ?)");
                    $stmt2->execute([$email, $new_user_id]);
                }

                $pdo->commit();
                $response = ["status" => "success", "message" => "Η εγγραφή ολοκληρώθηκε!"];
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $response["message"] = "Σφάλμα συστήματος: " . $e->getMessage();
        }
    }
}

echo json_encode($response);