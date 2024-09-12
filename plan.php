<?php include('_templates/head.php'); ?>
<?php include('_templates/header.php'); ?>
<?php include('libs/load.php'); ?> <!-- Include your database connection -->

<?php
$conn = Database::getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $week_id = $_POST['week'];
    $team_id = $_POST['team'];
    $emp_id = $_POST['employee'];
    $day = $_POST['day'];
    $hours = $_POST['hours'];

    // Validate that all fields are provided
    if (!empty($week_id) && !empty($team_id) && !empty($emp_id) && !empty($day) && !empty($hours)) {
        // Prepare the SQL query to insert data into work_hours table
        $sql = "INSERT INTO work_hours (week_id, team_id, emp_id, day, hours) VALUES (?, ?, ?, ?, ?)";

        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiiss', $week_id, $team_id, $emp_id, $day, $hours); // 'iiiss' means 2 integers, 1 string for the day, and 1 string for hours

        // Execute the query and check if the data was inserted
        if ($stmt->execute()) {
            echo '<div class="alert alert-success">Plan added successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Failed to add plan. Please try again later.</div>';
        }

        $stmt->close();
    } else {
        echo '<div class="alert alert-danger">Please fill all the fields.</div>';
    }
}

// Fetch consolidated hours for each employee for the selected week (modified for each day)
$hours_sql = "SELECT day, hours FROM work_hours WHERE emp_id = ? AND week_id = ?";
$stmt = $conn->prepare($hours_sql);
$stmt->bind_param('ii', $emp_id, $week_id); // Bind parameters
$stmt->execute();
$hours_result = $stmt->get_result();
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
                    $sql = "SELECT id, week_name, from_date, to_date FROM list_wee";
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
                <input type="text" class="form-control" id="hours" name="hours" placeholder="Hours (e.g., 07:30)" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-block">Add Plan</button>
    </form>

    <!-- Consolidated Plans Section -->
    <h3 class="mt-5">Consolidated Plans</h3>
    <?php
    if (isset($_POST['week'])) {
        $week_id = $_POST['week'];

        // Fetch teams and employees for the selected week
        $teams_sql = "SELECT teams.team_name, employees.employee_name, employees.id AS emp_id
                      FROM teams
                      JOIN employees ON teams.id = employees.team_id
                      ORDER BY teams.id";
        $teams_result = $conn->query($teams_sql);

        $current_team = '';
        $team_total_hours = 0;

        if ($teams_result->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead>
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
              </thead>';
            echo '<tbody>';

            while ($row = $teams_result->fetch_assoc()) {
                $emp_id = $row['emp_id'];
                $emp_name = $row['employee_name'];
                $team_name = $row['team_name'];

                if ($current_team != $team_name) {
                    if ($current_team != '') {
                        echo "<tr><td colspan='9'>Total for $current_team: $team_total_hours hours</td></tr>";
                    }
                    echo "<tr><th colspan='9'>$team_name</th></tr>";
                    $current_team = $team_name;
                    $team_total_hours = 0;
                }

                // Fetch hours for each employee for the week
                $hours_sql = "SELECT day, hours FROM work_hours WHERE emp_id = ? AND week_id = ?";
                $stmt = $conn->prepare($hours_sql);
                $stmt->bind_param('ii', $emp_id, $week_id);
                $stmt->execute();
                $hours_result = $stmt->get_result();

                $days_hours = array_fill(0, 7, '00:00');
                $total_hours = 0;

                if ($hours_result->num_rows > 0) {
                    while ($hours_row = $hours_result->fetch_assoc()) {
                        $day = $hours_row['day'];
                        $hours = $hours_row['hours'];
                        $days_hours[array_search($day, ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'])] = $hours;
                        $total_hours += strtotime($hours) - strtotime('TODAY');
                    }
                }

                $total_hours = gmdate('H:i', $total_hours);

                echo "<tr>
                    <td>$emp_name</td>";
                foreach ($days_hours as $day_hours) {
                    echo "<td>$day_hours</td>";
                }
                echo "<td>$total_hours</td></tr>";

                $team_total_hours += strtotime($total_hours) - strtotime('TODAY');
            }

            echo "<tr><td colspan='9'>Total for $current_team: " . gmdate('H:i', $team_total_hours) . " hours</td></tr>";
            echo '</tbody>';
            echo '</table>';
        }
    }
    ?>
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
