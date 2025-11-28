<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form fields
    $student_id = trim($_POST['student_id'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $group_name = trim($_POST['group_name'] ?? '');

    // Validation
    if (!is_numeric($student_id) || empty($firstname) || empty($lastname) || empty($group_name)) {
        echo "<p style='color:red'>Please provide valid data.</p>";
        exit;
    }

    // Insert into DB
    $stmt = $conn->prepare(
        "INSERT INTO students (student_id, firstname, lastname, email, group_name) 
         VALUES (?, ?, ?, ?, ?)"
    );

    try {
        $stmt->execute([$student_id, $firstname, $lastname, $email, $group_name]);
        echo "<p style='color:green'>Student added successfully!</p>";
        echo "<a href='index.php'>Back to List</a>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}
?>