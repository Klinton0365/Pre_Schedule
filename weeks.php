<?php
$weeks_file = 'data/weeks.txt';

function getWeeks() {
    global $weeks_file;
    return file($weeks_file, FILE_IGNORE_NEW_LINES);
}

function addWeek($week) {
    global $weeks_file;
    file_put_contents($weeks_file, $week.PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $week_name = $_POST['week_name'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $week = "$week_name,$from_date,$to_date";
    addWeek($week);
}

$weeks = getWeeks();
?>

<?php include('includes/header.php'); ?>
<div class="container">
    <h2>Weeks Management</h2>
    <form method="POST">
        <input type="text" name="week_name" placeholder="Week Name" required>
        <input type="date" name="from_date" required>
        <input type="date" name="to_date" required>
        <button type="submit" class="btn btn-success">Add Week</button>
    </form>

    <h3>List of Weeks</h3>
    <ul>
        <?php foreach ($weeks as $week): ?>
            <li><?= htmlspecialchars($week) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php include('includes/footer.php'); ?>
