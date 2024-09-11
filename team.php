<?php
$teams_file = 'data/teams.txt';

function getTeams() {
    global $teams_file;
    return file($teams_file, FILE_IGNORE_NEW_LINES);
}

function addTeam($team) {
    global $teams_file;
    file_put_contents($teams_file, $team.PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $team_name = $_POST['team_name'];
    addTeam($team_name);
}

$teams = getTeams();
?>

<?php include('includes/header.php'); ?>
<div class="container">
    <h2>Teams Management</h2>
    <form method="POST">
        <input type="text" name="team_name" placeholder="Team Name" required>
        <button type="submit" class="btn btn-success">Add Team</button>
    </form>

    <h3>List of Teams</h3>
    <ul>
        <?php foreach ($teams as $team): ?>
            <li><?= htmlspecialchars($team) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php include('includes/footer.php'); ?>
