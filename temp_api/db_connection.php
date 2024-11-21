<?php
// db_connection.php
$host = 'localhost';
$dbname = 'assign_db';
$username = 'root'; // Adjust if needed
$password = ''; // Adjust if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>
