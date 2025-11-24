<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
</head>
<body>
<h2>Add Student</h2>

<form method="post" action="">
    Student ID: <input type="text" name="student_id" required><br><br>
    Full Name: <input type="text" name="name" required><br><br>
    Group: <input type="text" name="group" required><br><br>
    <input type="submit" value="Add Student">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $group = trim($_POST['group']);

    // 1️⃣ Validation
    if (!is_numeric($student_id)) {
        echo "<p style='color:red'>Student ID must be numeric.</p>";
        exit;
    }

    if (empty($name) || empty($group)) {
        echo "<p style='color:red'>Name and Group are required.</p>";
        exit;
    }

    // 2️⃣ Load students.json
    $file = 'students.json';
    $students = [];
    if (file_exists($file)) {
        $data = file_get_contents($file);
        $students = json_decode($data, true) ?? [];
    }

    // 3️⃣ Add new student
    $students[] = [
        'student_id' => $student_id,
        'name' => $name,
        'group' => $group
    ];

    // 4️⃣ Save back to JSON
    file_put_contents($file, json_encode($students, JSON_PRETTY_PRINT));

    // 5️⃣ Confirmation
    echo "<p style='color:green'>Student $name added successfully!</p>";
}
?>

</body>
</html>
