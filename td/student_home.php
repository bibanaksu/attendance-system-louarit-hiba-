<?php
session_start();
require 'db_connect.php';
if ($_SESSION['role'] !== 'student') { header('Location: login.php'); exit(); }

// Get student's enrolled courses
$stmt = $conn->prepare("
    SELECT c.*, g.group_name 
    FROM courses c 
    JOIN enrollments e ON c.course_id = e.course_id 
    JOIN groups g ON e.group_id = g.group_id 
    WHERE e.student_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Home</title>
</head>
<body>
    <h1>Welcome, <?= $_SESSION['name'] ?></h1>
    <nav>
        <a href="student_home.php">Home</a> |
        <a href="logout.php">Logout</a>
    </nav>

    <h2>My Courses</h2>
    <?php foreach ($courses as $course): ?>
    <div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0;">
        <h3><?= $course['course_name'] ?> (<?= $course['course_code'] ?>)</h3>
        <p>Group: <?= $course['group_name'] ?></p>
        <a href="student_attendance.php?course_id=<?= $course['course_id'] ?>">View Attendance</a>
    </div>
    <?php endforeach; ?>
</body>
</html>