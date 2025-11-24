<?php
$file = 'students.json';
$students = [];

if (file_exists($file)) {
    $students = json_decode(file_get_contents($file), true);
} else {
    echo "No students found. Add students first.";
    exit;
}

// File for today
$today_file = 'attendance_' . date('Y-m-d') . '.json';

if (file_exists($today_file)) {
    echo "<p>Attendance for today has already been taken.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance = [];
    foreach ($students as $student) {
        $status = $_POST['status'][$student['student_id']] ?? 'absent';
        $attendance[] = [
            'student_id' => $student['student_id'],
            'status' => $status
        ];
    }

    file_put_contents($today_file, json_encode($attendance, JSON_PRETTY_PRINT));
    echo "<p>Attendance saved successfully!</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>
</head>
<body>
<h2>Take Attendance</h2>
<form method="post">
    <table border="1" cellpadding="5">
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Group</th>
            <th>Status</th>
        </tr>
        <?php foreach($students as $s): ?>
        <tr>
            <td><?php echo $s['student_id']; ?></td>
            <td><?php echo $s['name']; ?></td>
            <td><?php echo $s['group']; ?></td>
            <td>
                <select name="status[<?php echo $s['student_id']; ?>]">
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                </select>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <input type="submit" value="Submit Attendance">
</form>
</body>
</html>
