<?php
require 'config.php';

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    // Optional: log to file
    file_put_contents('db_error.log', $e->getMessage(), FILE_APPEND);
}
?>

