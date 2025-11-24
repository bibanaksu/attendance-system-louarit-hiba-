const tableBody = document.querySelector('#attendanceTable tbody');
const studentForm = document.getElementById('studentForm');
const showReportBtn = document.getElementById('showReportBtn');
const totalStudentsEl = document.getElementById('totalStudents');
const totalPresentEl = document.getElementById('totalPresent');
const totalParticipationEl = document.getElementById('totalParticipation');
const reportChartEl = document.getElementById('reportChart');

let reportChart;

// ===== Update Row Function =====
function updateRow(row) {
    const sessionCheckboxes = row.querySelectorAll('.session');
    const participationCheckboxes = row.querySelectorAll('.participation');

    let presentCount = 0;
    sessionCheckboxes.forEach(cb => { if (cb.checked) presentCount++; });
    const totalSessions = sessionCheckboxes.length;
    const absences = totalSessions - presentCount;

    let participation = 0;
    participationCheckboxes.forEach(cb => { if (cb.checked) participation++; });

    row.cells[14].textContent = absences;
    row.cells[15].textContent = participation;

    row.classList.remove('green','yellow','red');
    if (absences < 3) row.classList.add('green');
    else if (absences <= 4) row.classList.add('yellow');
    else row.classList.add('red');

    let message = "";
    if (absences <= 1 && participation >= 5)
        message = "Excellent – Perfect attendance and active participation";
    else if (absences <= 1 && participation >= 3)
        message = "Very good – Perfect attendance but participation can improve";
    else if (absences <= 3 && participation >= 4)
        message = "Good – Regular attendance and good participation";
    else if (absences <= 3 && participation < 4)
        message = "Fair – Attendance is okay but participation is low";
    else if (absences <= 5 && participation >= 4)
        message = "Warning – Many absences, participation is okay";
    else if (absences <= 5 && participation < 4)
        message = "Alert – Low attendance and low participation";
    else if (absences >= 6)
        message = "Critical – Missed all sessions";

    row.cells[16].textContent = message;
}

// ===== Attach checkbox listeners =====
function attachCheckboxListeners(row) {
    row.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', () => updateRow(row));
    });
}

// ===== Hover highlight & click =====
function attachRowInteractions(row) {
    row.addEventListener('mouseenter', () => {
        row.dataset.originalColor = row.style.backgroundColor;
        row.style.backgroundColor = '#cce5ff';
    });

    row.addEventListener('mouseleave', () => {
        row.style.backgroundColor = '';
    });

    row.addEventListener('click', e => {
        if (e.target.tagName.toLowerCase() === 'input') return;
        const lastName = row.cells[0].textContent;
        const firstName = row.cells[1].textContent;
        const absences = row.cells[14].textContent;
        alert(`Student: ${firstName} ${lastName}\nAbsences: ${absences}`);
    });
}

// ===== Initialize existing rows =====
if (tableBody) {
    tableBody.querySelectorAll('tr').forEach(row => {
        attachCheckboxListeners(row);
        attachRowInteractions(row);
    });
}

// ===== Add student =====
if (studentForm) {
    studentForm.addEventListener('submit', e => {
        e.preventDefault();

        const studentId = document.getElementById('studentId').value.trim();
        const lastName = document.getElementById('lastName').value.trim();
        const firstName = document.getElementById('firstName').value.trim();
        const email = document.getElementById('email').value.trim();

        if (!/^\d+$/.test(studentId)) { alert("Student ID must be a number"); return; }
        if (!/^[A-Za-z]+$/.test(lastName)) { alert("Last Name must contain only letters"); return; }
        if (!/^[A-Za-z]+$/.test(firstName)) { alert("First Name must contain only letters"); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { alert("Email is invalid"); return; }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${lastName}</td>
            <td>${firstName}</td>
            ${'<td><input type="checkbox" class="session"></td><td><input type="checkbox" class="participation"></td>'.repeat(6)}
            <td>0</td>
            <td>0</td>
            <td></td>
        `;
        tableBody.appendChild(newRow);
        attachCheckboxListeners(newRow);
        attachRowInteractions(newRow);
        updateRow(newRow);
        studentForm.reset();
    });
}

// ===== REPORT CHART =====
if (showReportBtn) {
    showReportBtn.addEventListener('click', () => {
        const rows = tableBody.querySelectorAll('tr');
        const totalStudents = rows.length;
        let totalPresent = 0;
        let totalParticipation = 0;

        rows.forEach(row => {
            row.querySelectorAll('.session').forEach(cb => { if (cb.checked) totalPresent++; });
            row.querySelectorAll('.participation').forEach(cb => { if (cb.checked) totalParticipation++; });
        });

        totalStudentsEl.textContent = totalStudents;
        totalPresentEl.textContent = totalPresent;
        totalParticipationEl.textContent = totalParticipation;

        if (reportChart) reportChart.destroy();

        reportChart = new Chart(reportChartEl, {
            type: 'bar',
            data: {
                labels: ['Students', 'Present', 'Participation'],
                datasets: [{
                    label: 'Attendance Report',
                    data: [totalStudents, totalPresent, totalParticipation],
                    backgroundColor: ['#9D8361', '#d4edda', '#fff3cd'],
                    borderColor: ['#66281F', '#28a745', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
}

// ===================================================
//  ALL JQUERY FUNCTIONS — merged in ONE block
// ===================================================
$(document).ready(function() {

    // ---- SEARCH ----
    $('#searchName').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#attendanceTable tbody tr').filter(function() {
            const ln = $(this).find('td').eq(0).text().toLowerCase();
            const fn = $(this).find('td').eq(1).text().toLowerCase();
            return ln.includes(searchTerm) || fn.includes(searchTerm);
        }).show().end().not(function() {
            const ln = $(this).find('td').eq(0).text().toLowerCase();
            const fn = $(this).find('td').eq(1).text().toLowerCase();
            return ln.includes(searchTerm) || fn.includes(searchTerm);
        }).hide();
    });

    // ---- SORT ABSENCES ASC ----
    $('#sortAbsences').click(function() {
        const rows = $('#attendanceTable tbody tr').get();
        rows.sort((a, b) => 
            parseInt($(a).children('td').eq(14).text()) -
            parseInt($(b).children('td').eq(14).text())
        );
        $('#attendanceTable tbody').append(rows);
        $('#sortMessage').text("Currently sorted by absences (ascending)");
    });

    // ---- SORT PARTICIPATION DESC ----
    $('#sortParticipation').click(function() {
        const rows = $('#attendanceTable tbody tr').get();
        rows.sort((a, b) => 
            parseInt($(b).children('td').eq(15).text()) -
            parseInt($(a).children('td').eq(15).text())
        );
        $('#attendanceTable tbody').append(rows);
        $('#sortMessage').text("Currently sorted by participation (descending)");
    });

    // ---- HIGHLIGHT EXCELLENT ----
    $('#highlightExcellent').click(function() {
        $('#attendanceTable tbody tr').each(function() {
            if (parseInt($(this).find('td').eq(14).text()) < 3) {
                $(this).addClass('highlight');
                setTimeout(() => $(this).removeClass('highlight'), 3000);
            }
        });
    });

    // ---- RESET COLORS ----
    $('#resetColors').click(function() {
        $('#attendanceTable tbody tr').removeClass('highlight');
    });
});

// ===== PAGE SWITCHING =====
const pages = document.querySelectorAll('.page');
const navLinks = document.querySelectorAll('.nav-link');

function showPage(pageId) {
    pages.forEach(p => p.style.display = 'none');
    const page = document.getElementById(pageId);
    if(page) page.style.display = 'block';
}

navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        showPage(link.dataset.page);
    });
});

// Show Home page by default
showPage('home');
