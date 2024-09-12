<?php include('_templates/head.php'); ?>
<?php include('_templates/header.php'); ?>
<?php include('libs/load.php'); ?>

<?php
$conn = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $week_id = $_POST['week'];
    $team_id = $_POST['team'];
    $emp_id = $_POST['employee'];
    $day = strtolower($_POST['day']); // Ensure day is in lowercase to match table column names
    $hours = $_POST['hours'];

    // Validate that all fields are provided
    if (!empty($week_id) && !empty($team_id) && !empty($emp_id) && !empty($day) && !empty($hours)) {
        // Prepare the week table name based on the selected week
        $week_table_sql = "SELECT week_name FROM list_wee WHERE id = ?";
        $stmt = $conn->prepare($week_table_sql);
        $stmt->bind_param('i', $week_id);
        $stmt->execute();
        $week_table_result = $stmt->get_result();
        $week_row = $week_table_result->fetch_assoc();
        $table_name = "week_table_" . $week_row['week_name'];

        // Check if the table exists
        $check_table_sql = "SHOW TABLES LIKE '$table_name'";
        $result = $conn->query($check_table_sql);

        if ($result->num_rows > 0) {
            // Check if the employee already exists in this week's table
            $check_emp_sql = "SELECT emp_id FROM `$table_name` WHERE emp_id = ?";
            $stmt = $conn->prepare($check_emp_sql);
            $stmt->bind_param('i', $emp_id);
            $stmt->execute();
            $emp_result = $stmt->get_result();

            if ($emp_result->num_rows > 0) {
                // If employee exists, update the specific day
                $update_sql = "UPDATE `$table_name` SET $day = ? WHERE emp_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param('si', $hours, $emp_id);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-success">Plan updated successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to update plan. Please try again.</div>';
                }
            } else {
                // If employee does not exist, insert a new record
                $insert_sql = "INSERT INTO `$table_name` (emp_id, team_id, team_name, $day) 
                               VALUES (?, ?, (SELECT team_name FROM teams WHERE id = ?), ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param('iiis', $emp_id, $team_id, $team_id, $hours);

                if ($stmt->execute()) {
                    echo '<div class="alert alert-success">Plan added successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger">Failed to add plan. Please try again.</div>';
                }
            }
        } else {
            echo '<div class="alert alert-danger">Selected week table does not exist. Please check the week selection.</div>';
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
                        <option value="<?= $row['id'] ?>">
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
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['team_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

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
                <input type="text" class="form-control" id="hours" name="hours" placeholder="Hours (e.g., 07:30)"
                    required>
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
document.getElementById('consolidatedWeek').addEventListener('change', function () {
    const weekId = this.value;
    if (weekId) {
        // Make an API call to get_week_data.php to fetch the data
        fetch('get_week_data.php?week_id=' + weekId)
            .then(response => response.json())
            .then(data => {
                let table = `<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
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
                let currentTeam = '';
                let teamTotalHours = 0;

                data.forEach(item => {
                    // If team changes, add a row with the team's total hours
                    if (currentTeam !== item.team_name) {
                        if (currentTeam) {
                            table += `<tr><td colspan="9">Total for ${currentTeam}: ${teamTotalHours} hours</td></tr>`;
                        }
                        table += `<tr><th colspan="9">${item.team_name}</th></tr>`;
                        currentTeam = item.team_name;
                        teamTotalHours = 0;
                    }

                    // Add employee data
                    let totalHours = 0;
                    table += `<tr><td>${item.employee_name}</td>`;
                    ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'].forEach(day => {
                        let hours = item[day] || '00:00'; // Default to '00:00' if no data
                        table += `<td>${hours}</td>`;
                        totalHours += parseFloat(hours); // Assuming hours are in decimal format (e.g., 7.5)
                    });
                    table += `<td>${totalHours}</td></tr>`;
                    teamTotalHours += totalHours;
                });

                table += `</tbody></table>`;
                document.getElementById('consolidatedTable').innerHTML = table;
            })
            .catch(error => console.error('Error fetching data:', error));
    }
});
</script>



    <!-- Add logic for displaying consolidated data similar to the current week's table -->
</div>

<script>
    document.getElementById('planForm').addEventListener('submit', function (e) {
        let week = document.getElementById('week').value;
        let team = document.getElementById('team').value;
        let employee = document.getElementById('employee').value;
        let hours = document.getElementById('hours').value;
        if (!week || !team || !employee || !hours) {
            alert('Please fill all the fields!');
            e.preventDefault();
        }
    });
</script>

<?php include('_templates/footer.php'); ?>