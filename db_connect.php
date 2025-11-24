<?php
// db_connect.php
$host = 'localhost';
$dbname = 'attendance_db'; // Make sure this is correct
$username = 'root'; // Default WAMP username
$password = ''; // Default WAMP password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>