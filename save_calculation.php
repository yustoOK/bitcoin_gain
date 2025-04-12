<?php
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
    file_put_contents('db_log.txt', "Database connection successful\n", FILE_APPEND);
} catch(PDOException $e) {
    file_put_contents('db_error.log', "Connection failed: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Handle AJAX request to store data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_calculation'])) {
    file_put_contents('db_log.txt', "Received POST request\n", FILE_APPEND);
    file_put_contents('db_log.txt', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

    try {
        // Validate and sanitize inputs
        $required_fields = [
            'btc_bought', 'tzs_value_2030_mid', 'tzs_gain_mid',
            'tzs_value_2030_opt', 'tzs_gain_opt', 'tzs_value_2030_con',
            'tzs_gain_con', 'tzs_value_2030_avg', 'tzs_gain_avg'
        ];

        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || !is_numeric($_POST[$field])) {
                throw new Exception("Missing or invalid field: $field");
            }
        }

        $btc_bought = floatval($_POST['btc_bought']);
        $tzs_value_2030_mid = floatval($_POST['tzs_value_2030_mid']);
        $tzs_gain_mid = floatval($_POST['tzs_gain_mid']);
        $tzs_value_2030_opt = floatval($_POST['tzs_value_2030_opt']);
        $tzs_gain_opt = floatval($_POST['tzs_gain_opt']);
        $tzs_value_2030_con = floatval($_POST['tzs_value_2030_con']);
        $tzs_gain_con = floatval($_POST['tzs_gain_con']);
        $tzs_value_2030_avg = floatval($_POST['tzs_value_2030_avg']);
        $tzs_gain_avg = floatval($_POST['tzs_gain_avg']);

        file_put_contents('db_log.txt', "Parsed values: " . print_r([
            'btc_bought' => $btc_bought,
            'tzs_value_2030_mid' => $tzs_value_2030_mid,
            'tzs_gain_mid' => $tzs_gain_mid,
            'tzs_value_2030_opt' => $tzs_value_2030_opt,
            'tzs_gain_opt' => $tzs_gain_opt,
            'tzs_value_2030_con' => $tzs_value_2030_con,
            'tzs_gain_con' => $tzs_gain_con,
            'tzs_value_2030_avg' => $tzs_value_2030_avg,
            'tzs_gain_avg' => $tzs_gain_avg
        ], true) . "\n", FILE_APPEND);

        $stmt = $conn->prepare("INSERT INTO calculations (btc_bought, tzs_value_2030_mid, tzs_gain_mid, tzs_value_2030_opt, tzs_gain_opt, tzs_value_2030_con, tzs_gain_con, tzs_value_2030_avg, tzs_gain_avg) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$btc_bought, $tzs_value_2030_mid, $tzs_gain_mid, $tzs_value_2030_opt, $tzs_gain_opt, $tzs_value_2030_con, $tzs_gain_con, $tzs_value_2030_avg, $tzs_gain_avg]);

        file_put_contents('db_log.txt', "Data inserted successfully\n", FILE_APPEND);
        echo json_encode(['status' => 'success', 'message' => 'Calculation saved successfully']);
    } catch(Exception $e) {
        file_put_contents('db_error.log', "Insert failed: " . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Failed to save calculation: ' . $e->getMessage()]);
    }
} else {
    file_put_contents('db_error.log', "Invalid request: " . print_r($_SERVER, true) . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>