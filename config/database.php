<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'petrex_estate');
define('SITE_URL', 'https://petrex-estate.com');
define('SITE_NAME', 'Petrex Estate and Property Managers');

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Database connection error. Please try again later.");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
