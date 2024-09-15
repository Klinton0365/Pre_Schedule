<?php
Session::loadTemplate('head');
Session::loadTemplate('header');
?>
<?php include('libs/load.php'); ?> 

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

    <!-- PHP code to handle form submission, deletion, and updates -->
    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['delete_employee'])) {
            // Delete employee logic
            $employee_id = $_POST['delete_employee'];
            $sql = "DELETE FROM employees WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $employee_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Employee deleted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        } elseif (isset($_POST['update_employee'])) {
            // Update employee logic
            $employee_id = $_POST['employee_id'];
            $employee_name = $_POST['employee_name'];
            $team_id = $_POST['team'];

            $sql = "UPDATE employees SET employee_name = ?, team_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $employee_name, $team_id, $employee_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Employee updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        } else {
            // Insert new employee
            $employee_name = $_POST['employee_name'];
            $team_id = $_POST['team'];

            $sql = "INSERT INTO employees (employee_name, team_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $employee_name, $team_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Employee added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        }
    }

    // Fetch the list of employees along with their team names
    $sql = "SELECT employees.id, employees.employee_name, teams.team_name 
            FROM employees 
            JOIN teams ON employees.team_id = teams.id 
            ORDER BY employees.id DESC";
    $result = $conn->query($sql);
    ?>

    <!-- Display list of employees with Update and Delete buttons -->
    <h3 class="mt-5">List of Employees</h3>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($row['employee_name']) . ' (Team: ' . htmlspecialchars($row['team_name']) . ')' ?></span>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="showUpdateModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['employee_name']) ?>', <?= $row['id'] ?>)">Update</button>
                        <form action="employee.php" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_employee" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item">No employees found.</li>
        <?php endif; ?>
    </ul>
</div>

<!-- Modal for updating the employee -->
<div class="modal fade" id="updateEmployeeModal" tabindex="-1" aria-labelledby="updateEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="employee.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateEmployeeModalLabel">Update Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="employee_id" name="employee_id">
                    <div class="form-group">
                        <label for="employee_name">Employee Name</label>
                        <input type="text" class="form-control" id="modal_employee_name" name="employee_name" required>
                    </div>
                    <div class="form-group">
                        <label for="team">Select Team</label>
                        <select class="form-control" id="modal_team" name="team" required>
                            <option value="">Select Team</option>
                            <?php
                            // Populate the teams dropdown again for the modal
                            $sql = "SELECT id, team_name FROM teams";
                            $result_teams = $conn->query($sql);
                            while ($row_team = $result_teams->fetch_assoc()):
                            ?>
                                <option value="<?= $row_team['id'] ?>"><?= htmlspecialchars($row_team['team_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_employee" class="btn btn-primary">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JavaScript to show the update modal and populate the fields with the current employee data
function showUpdateModal(id, name, team_id) {
    document.getElementById('employee_id').value = id;
    document.getElementById('modal_employee_name').value = name;
    document.getElementById('modal_team').value = team_id;
    $('#updateEmployeeModal').modal('show');
}

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

<!-- Include Bootstrap's JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php Session::loadTemplate('footer'); ?>
