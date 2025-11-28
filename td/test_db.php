<?php
require 'db_connect.php';

$stmt = $conn->query("SELECT * FROM students LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($row);
?>
