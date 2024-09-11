<?php
$employees_file = 'data/employees.txt';

function getEmployees() {
    global $employees_file;
    return file($employees_file, FILE_IGNORE_NEW_LINES);
}

function addEmployee($employee) {
    global $employees_file;
    file_put_contents($employees_file, $employee.PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_name = $_POST['employee_name'];
    $team = $_POST['team'];
    $employee = "$employee_name,$team";
    addEmployee($employee);
}

$employees = getEmployees();
$teams = file('data/teams.txt', FILE_IGNORE_NEW_LINES);
?>

<?php include('includes/header.php'); ?>
<div class="container">
    <h2>Employee Management</h2>
    <form method="POST">
        <input type="text" name="employee_name" placeholder="Employee Name" required>
        <select name="team" required>
            <?php foreach ($teams as $team): ?>
                <option value="<?= htmlspecialchars($team) ?>"><?= htmlspecialchars($team) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-success">Add Employee</button>
    </form>

    <h3>List of Employees</h3>
    <ul>
        <?php foreach ($employees as $employee): ?>
            <li><?= htmlspecialchars($employee) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php include('includes/footer.php'); ?>
