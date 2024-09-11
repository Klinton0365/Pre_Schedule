<?php include('_templates/head.php'); ?>
<?php include('_templates/header.php'); ?>
<?php include('libs/load.php'); ?> <!-- Database connection file -->

<div class="container mt-5">
    <h2 class="text-center">Employee Management</h2>

    <!-- Form to add a new employee -->
    <form action="employee.php" class="mt-4" method="POST" id="employeeForm">
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee Name" required>
            </div>
            <div class="col-md-4 mb-3">
                <!-- Dropdown populated with teams -->
                <select class="form-control" id="team" name="team" required>
                    <option value="">Select Team</option>
                    <?php
                    $conn = Database::getConnection();
                    // Fetch teams from the database
                    $sql = "SELECT id, team_name FROM teams";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['team_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success btn-block">Add Employee</button>
            </div>
        </div>
    </form>

    <!-- PHP code to handle form submission -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $employee_name = $_POST['employee_name'];
        $team_id = $_POST['team'];

        // Insert employee into the database
        $sql = "INSERT INTO employees (employee_name, team_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $employee_name, $team_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Employee added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }

    // Fetch the list of employees along with their team names
    $sql = "SELECT employees.id, employees.employee_name, teams.team_name 
            FROM employees 
            JOIN teams ON employees.team_id = teams.id 
            ORDER BY employees.id DESC";
    $result = $conn->query($sql);
    ?>

    <!-- Display list of employees -->
    <h3 class="mt-5">List of Employees</h3>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($row['employee_name']) . ' (Team: ' . htmlspecialchars($row['team_name']) . ')' ?>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item">No employees found.</li>
        <?php endif; ?>
    </ul>
</div>

<script>
// JavaScript validation to ensure the employee name and team are filled
document.getElementById('employeeForm').addEventListener('submit', function(e) {
    let employeeName = document.getElementById('employee_name').value;
    let team = document.getElementById('team').value;
    if (!employeeName || !team) {
        alert('Please fill all the fields!');
        e.preventDefault();
    }
});
</script>

<?php include('_templates/footer.php'); ?>
