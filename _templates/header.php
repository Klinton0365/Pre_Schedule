<?php
$plans_file = 'data/plans.txt';

function getPlans() {
    global $plans_file;
    return file($plans_file, FILE_IGNORE_NEW_LINES);
}

function addPlan($plan) {
    global $plans_file;
    file_put_contents($plans_file, $plan.PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $week = $_POST['week'];
    $team = $_POST['team'];
    $employee = $_POST['employee'];
    $hours = $_POST['hours'];
    $plan = "$week,$team,$employee,$hours";
    addPlan($plan);
}

$plans = getPlans();
$weeks = file('data/weeks.txt', FILE_IGNORE_NEW_LINES);
$teams = file('data/teams.txt', FILE_IGNORE_NEW_LINES);
$employees = file('data/employees.txt', FILE_IGNORE_NEW_LINES);
?>

<?php include('includes/header.php'); ?>
<div class="container">
    <h2>Plan for the Week</h2>
    <form method="POST">
        <select name="week" required>
            <?php foreach ($weeks as $week): ?>
                <option value="<?= htmlspecialchars($week) ?>"><?= htmlspecialchars($week) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="team" required>
            <?php foreach ($teams as $team): ?>
                <option value="<?= htmlspecialchars($team) ?>"><?= htmlspecialchars($team) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="employee" required>
            <?php foreach ($employees as $employee): ?>
                <option value="<?= htmlspecialchars($employee) ?>"><?= htmlspecialchars($employee) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="hours" placeholder="Hours" required>
        <button type="submit" class="btn btn-success">Add Plan</button>
    </form>

    <h3>Consolidated Plans</h3>
    <ul>
        <?php foreach ($plans as $plan): ?>
            <li><?= htmlspecialchars($plan) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php include('includes/footer.php'); ?>
