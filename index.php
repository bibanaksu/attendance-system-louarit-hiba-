<?php
require 'db_connect.php';

// ===== Handle Add Student Form =====
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $email     = trim($_POST['email'] ?? null);
    $groupName = trim($_POST['groupName'] ?? '');

    if (!$firstName || !$lastName || !$groupName) {
        $message = "<p style='color:red'>Please fill in all required fields!</p>";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO students (firstname, lastname, email, group_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $groupName]);
            $message = "<p style='color:green'>Student $firstName $lastName added successfully!</p>";
        } catch (PDOException $e) {
            $message = "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
        }
    }
}

// Fetch all students for both tables
try {
    $stmt = $conn->query('SELECT * FROM students ORDER BY `lastname`, `firstname`');
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<!-- Navbar -->
<nav class="navbar">
    <a href="#" class="nav-link" data-page="home">Home</a>
    <a href="#" class="nav-link" data-page="attendanceList">Attendance List</a>
    <a href="#" class="nav-link" data-page="addStudent">Add Student</a>
    <a href="#" class="nav-link" data-page="reports">Reports</a>
    <a href="#" class="nav-link" data-page="logout">Logout</a>
</nav>

<header>
    <h1>Attendance System</h1>
</header>

<!-- HOME PAGE -->
<div id="home" class="page">
    

    <div id="searchContainer">
        <label for="searchName">Search by Name:</label>
        <input type="text" id="searchName" placeholder="Type a name...">
    </div>

    <div id="sortButtons">
        <button id="sortAbsences">Sort by Absences (Ascending)</button>
        <button id="sortParticipation">Sort by Participation (Descending)</button>
    </div>
    <p id="sortMessage"></p>

    <table id="attendanceTable">
        <thead>
            <tr>
                <th rowspan="2">Last Name</th>
                <th rowspan="2">First Name</th>
                <th colspan="2">S1</th>
                <th colspan="2">S2</th>
                <th colspan="2">S3</th>
                <th colspan="2">S4</th>
                <th colspan="2">S5</th>
                <th colspan="2">S6</th>
                <th rowspan="2">Absences</th>
                <th rowspan="2">Participation</th>
                <th rowspan="2">Message</th>
            </tr>
            <tr>
                <?php for($i=0;$i<6;$i++): ?>
                    <th>P</th><th>Pa</th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['lastname']) ?></td>
                <td><?= htmlspecialchars($s['firstname']) ?></td>
                <?php for ($i=0; $i<6; $i++): ?>
                    <td><input type="checkbox" class="session"></td>
                    <td><input type="checkbox" class="participation"></td>
                <?php endfor; ?>
                <td>0</td>
                <td>0</td>
                <td></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align:center; margin:20px;">
        <button id="highlightExcellent">Highlight Excellent Students</button>
        <button id="resetColors">Reset Colors</button>
    </div>
</div>

<!-- ATTENDANCE LIST PAGE -->
<div id="attendanceList" class="page" style="display:none;">
   
    <table>
        <thead>
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Group</th>
                <th>Participations</th>
                <th>Absences</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['lastname']) ?></td>
                <td><?= htmlspecialchars($s['firstname']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['group_name']) ?></td>
                <td>0</td>
                <td>0</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ADD STUDENT PAGE -->
<div id="addStudent" class="page" style="display:none;">
    
    <?= $message ?>
    <form method="POST" id="studentForm">
        <label>First Name:</label>
        <input type="text" name="firstName" required>
        <label>Last Name:</label>
        <input type="text" name="lastName" required>
        <label>Email:</label>
        <input type="email" name="email">
        <label>Group:</label>
        <input type="text" name="groupName" required>
        <button type="submit">Add Student</button>
    </form>
</div>

<!-- REPORTS PAGE -->
<div id="reports" class="page" style="display:none;">
    
    <p>Total Students: <span id="totalStudents">0</span></p>
    <p>Total Present: <span id="totalPresent">0</span></p>
    <p>Total Participation: <span id="totalParticipation">0</span></p>
    <canvas id="reportChart"></canvas>
    <button id="showReportBtn">Show Report</button>
</div>

<!-- LOGOUT PAGE -->
<div id="logout" class="page" style="display:none;">
    <p style="text-align:center;">You have been logged out.</p>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="script.js"></script>

<script>
// Page switching
const pages = document.querySelectorAll('.page');
const navLinks = document.querySelectorAll('.nav-link');
function showPage(pageId) {
    pages.forEach(p => p.style.display = 'none');
    document.getElementById(pageId).style.display = 'block';
}
navLinks.forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        showPage(link.dataset.page);
    });
});
showPage('home');
</script>
</body>
</html>