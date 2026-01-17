<?php
$host = 'localhost';
$db   = 'university_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=university_db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>




