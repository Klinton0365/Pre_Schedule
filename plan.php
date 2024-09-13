<?php
include('_templates/head.php');
include('_templates/header.php');
include('libs/load.php');

$conn = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $week = $_POST['week'];
    $team_id = $_POST['team'];
    $team_name = $_POST['team_name']; // Capturing the team name
    $emp_id = $_POST['employee'];
    $day = strtolower($_POST['day']);
    $hours = $_POST['hours'];

    // Validate that all fields are provided
    if (!empty($week) && !empty($team_id) && !empty($team_name) && !empty($emp_id) && !empty($day) && !empty($hours)) {
        // Fetch employee name
        $emp_name_sql = "SELECT employee_name FROM employees WHERE id = ?";
        $stmt = $conn->prepare($emp_name_sql);
        $stmt->bind_param('i', $emp_id);
        $stmt->execute();
        $emp_name_result = $stmt->get_result();
        $emp_row = $emp_name_result->fetch_assoc();
        $employee_name = $emp_row['employee_name'];

        // Prepare the SQL query to insert or update the work hours table
        $table_name = "week_table_" . $week; 

        // Check if the employee exists in the selected team
        $check_sql = "SELECT id FROM `$table_name` WHERE emp_id = ? AND team_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('ii', $emp_id, $team_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Update existing record
            $update_sql = "UPDATE `$table_name` SET $day = ? WHERE emp_id = ? AND team_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param('sii', $hours, $emp_id, $team_id);
            if ($update_stmt->execute()) {
                echo '<div class="alert alert-success">Plan updated successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Failed to update plan. Please try again later.</div>';
            }
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO `$table_name` (emp_id, team_id, $day, employee_name, team_name) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param('iisss', $emp_id, $team_id, $hours, $employee_name, $team_name);
            if ($insert_stmt->execute()) {
                echo '<div class="alert alert-success">Plan added successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Failed to add plan. Please try again later.</div>';
            }
        }
    } else {
        echo '<div class="alert alert-danger">Please fill all the fields.</div>';
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Plan for the Week</h2>

    <!-- Form to select week, team, employee, day, and enter hours -->
    <form action="plan.php" class="mt-4" method="POST" id="planForm">
        <div class="form-row">
            <!-- Week Dropdown -->
            <div class="col-md-3 mb-3">
                <select class="form-control" id="week" name="week" required>
                    <option value="">Select Week</option>
                    <?php
                    // Fetch weeks from the database
                    $sql = "SELECT id, week_name, from_date, to_date FROM list_wee WHERE week_name LIKE 'week_%'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['week_name']) ?>">
                            <?= htmlspecialchars($row['week_name']) . ' (' . $row['from_date'] . ' - ' . $row['to_date'] . ')' ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Team Dropdown -->
            <div class="col-md-3 mb-3">
                <select class="form-control" id="team" name="team" required>
                    <option value="">Select Team</option>
                    <?php
                    // Fetch teams from the database
                    $sql = "SELECT id, team_name FROM teams";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" data-team-name="<?= htmlspecialchars($row['team_name']) ?>">
                            <?= htmlspecialchars($row['team_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="hidden" id="team_name" name="team_name">
            </div>
            <script>
                document.getElementById('team').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    document.getElementById('team_name').value = selectedOption.getAttribute('data-team-name');
                });
            </script>

            <!-- Employee Dropdown -->
            <div class="col-md-3 mb-3">
                <select class="form-control" id="employee" name="employee" required>
                    <option value="">Select Employee</option>
                    <?php
                    // Fetch employees from the database
                    $sql = "SELECT id, employee_name FROM employees";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['employee_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Day Dropdown -->
            <div class="col-md-3 mb-3">
                <select class="form-control" id="day" name="day" required>
                    <option value="">Select Day</option>
                    <option value="Sun">Sunday</option>
                    <option value="Mon">Monday</option>
                    <option value="Tue">Tuesday</option>
                    <option value="Wed">Wednesday</option>
                    <option value="Thu">Thursday</option>
                    <option value="Fri">Friday</option>
                    <option value="Sat">Saturday</option>
                </select>
            </div>

            <!-- Hours Input -->
            <div class="col-md-3 mb-3">
                <input type="text" class="form-control" id="hours" name="hours" placeholder="Hours (e.g., 07:30)" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-block">Add Plan</button>
    </form>

    <!-- Consolidated Plans Section -->
    <h3 class="mt-5">Consolidated Plans</h3>

    <!-- Week Dropdown -->
    <select class="form-control" id="consolidatedWeek" name="consolidatedWeek" required>
        <option value="">Select Week</option>
        <?php
        // Fetch weeks from the database
        $sql = "SELECT id, week_name, from_date, to_date FROM list_wee WHERE week_name LIKE 'week_%'";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>">
                <?= htmlspecialchars($row['week_name']) . ' (' . $row['from_date'] . ' - ' . $row['to_date'] . ')' ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- Section to display the consolidated table -->
    <div id="consolidatedTable" class="mt-4"></div>

    <script>
document.getElementById('consolidatedWeek').addEventListener('change', function() {
    const weekId = this.value;
    if (weekId) {
        fetch('get_week_data.php?week_id=' + weekId)
            .then(response => response.json())
            .then(data => {
                let table = `<table class="table table-bordered">
                    <thead style="background-color: #96DED1;">
                        <tr>
                            <th>#</th>
                            <th>Sun</th>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>`;

                let teamColors = {
                    'Marketing Team': '#E6F3FF',
                    'Techno Team': '#FFF2CC',
                    'Support Team': '#E2EFDA'
                };

                let currentTeam = '';
                let teamTotalHours = {
                    sun: 0,
                    mon: 0,
                    tue: 0,
                    wed: 0,
                    thu: 0,
                    fri: 0,
                    sat: 0,
                    total: 0
                };

                data.forEach((item, index) => {
                    if (currentTeam !== item.team_name) {
                        if (currentTeam) {
                            // Add team total row
                            table += `<tr style="background-color: #ADD8E6;">
                                <td>${currentTeam}</td>
                                <td>${formatTime(teamTotalHours.sun)}</td>
                                <td>${formatTime(teamTotalHours.mon)}</td>
                                <td>${formatTime(teamTotalHours.tue)}</td>
                                <td>${formatTime(teamTotalHours.wed)}</td>
                                <td>${formatTime(teamTotalHours.thu)}</td>
                                <td>${formatTime(teamTotalHours.fri)}</td>
                                <td>${formatTime(teamTotalHours.sat)}</td>
                                <td>${formatTime(teamTotalHours.total)}</td>
                            </tr>`;
                        }
                        // Reset team totals
                        teamTotalHours = {
                            sun: 0,
                            mon: 0,
                            tue: 0,
                            wed: 0,
                            thu: 0,
                            fri: 0,
                            sat: 0,
                            total: 0
                        };
                        currentTeam = item.team_name;
                    }

                    let employeeTotal = calculateTotalHours(item);

                    table += `<tr style="background-color: ${teamColors[item.team_name] || '#FFF'};">
                        <td>${item.employee_name}</td>
                        <td>${item.sun || ''}</td>
                        <td>${item.mon || ''}</td>
                        <td>${item.tue || ''}</td>
                        <td>${item.wed || ''}</td>
                        <td>${item.thu || ''}</td>
                        <td>${item.fri || ''}</td>
                        <td>${item.sat || ''}</td>
                        <td>${formatTime(employeeTotal)}</td>
                    </tr>`;

                    // Add to team total
                    teamTotalHours.sun += parseTime(item.sun);
                    teamTotalHours.mon += parseTime(item.mon);
                    teamTotalHours.tue += parseTime(item.tue);
                    teamTotalHours.wed += parseTime(item.wed);
                    teamTotalHours.thu += parseTime(item.thu);
                    teamTotalHours.fri += parseTime(item.fri);
                    teamTotalHours.sat += parseTime(item.sat);
                    teamTotalHours.total += employeeTotal;
                });

                if (currentTeam) {
                    // Add the final team total row
                    table += `<tr style="background-color: #ADD8E6;">
                        <td>${currentTeam}</td>
                        <td>${formatTime(teamTotalHours.sun)}</td>
                        <td>${formatTime(teamTotalHours.mon)}</td>
                        <td>${formatTime(teamTotalHours.tue)}</td>
                        <td>${formatTime(teamTotalHours.wed)}</td>
                        <td>${formatTime(teamTotalHours.thu)}</td>
                        <td>${formatTime(teamTotalHours.fri)}</td>
                        <td>${formatTime(teamTotalHours.sat)}</td>
                        <td>${formatTime(teamTotalHours.total)}</td>
                    </tr>`;
                }

                table += '</tbody></table>';
                document.getElementById('consolidatedTable').innerHTML = table;
            })
            .catch(error => console.error('Error:', error));
    }
});

// Function to calculate total hours for an employee
function calculateTotalHours(item) {
    return (parseTime(item.sun) || 0) +
        (parseTime(item.mon) || 0) +
        (parseTime(item.tue) || 0) +
        (parseTime(item.wed) || 0) +
        (parseTime(item.thu) || 0) +
        (parseTime(item.fri) || 0) +
        (parseTime(item.sat) || 0);
}

// Function to parse time string (HH:MM) into minutes
function parseTime(timeStr) {
    if (!timeStr || timeStr.trim() === '') return 0;
    const [hours, minutes] = timeStr.split(':').map(Number);
    return (hours || 0) * 60 + (minutes || 0);
}

// Function to format minutes into HH:MM
function formatTime(minutes) {
    if (minutes === 0 || isNaN(minutes)) return '';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
}


</script>



</div>

<?php include('_templates/footer.php'); ?>