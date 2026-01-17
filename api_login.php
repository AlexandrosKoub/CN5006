<?php
header('Content-Type: application/json');
require 'includes/config.php';
session_start();

$response = ["status" => "error", "message" => "Σφάλμα κατά τη σύνδεση."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $response["message"] = "Παρακαλώ συμπληρώστε όλα τα πεδία.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];

                $response = [
                    "status" => "success",
                    "message" => "Η σύνδεση ήταν επιτυχής!",
                    "role" => $user['role_id']
                ];
            } else {
                $response["message"] = "Λανθασμένο email ή κωδικός πρόσβασης.";
            }
        } catch (Exception $e) {
            $response["message"] = "Σφάλμα συστήματος: " . $e->getMessage();
        }
    }
}

echo json_encode($response);