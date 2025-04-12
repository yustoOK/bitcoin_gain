<?php
// Start output buffering to catch any unintended output
ob_start();

// Set the Content-Type header
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bitcoin_calculator";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    file_put_contents('db_log.txt', "Database connection successful (get_history)\n", FILE_APPEND);
} catch(PDOException $e) {
    file_put_contents('db_error.log', "Connection failed (get_history): " . $e->getMessage() . "\n", FILE_APPEND);
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT id, btc_bought, tzs_value_2030_mid, tzs_gain_mid, created_at FROM calculations ORDER BY created_at DESC");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    file_put_contents('db_log.txt', "History fetched successfully\n", FILE_APPEND);
    ob_end_clean();
    echo json_encode(['status' => 'success', 'data' => $results]);
} catch(Exception $e) {
    file_put_contents('db_error.log', "Fetch history failed: " . $e->getMessage() . "\n", FILE_APPEND);
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch history: ' . $e->getMessage()]);
}
?>