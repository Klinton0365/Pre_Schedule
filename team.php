<?php
include('libs/load.php');
Session::loadTemplate('head');
Session::loadTemplate('header');
?>


<div class="container mt-5">
    <h2 class="text-center">Teams Management</h2>

    <!-- Form to add a new team -->
    <form action="team.php" class="mt-4" method="POST" id="teamForm">
        <div class="form-row">
            <div class="col-md-8 mb-3">
                <input type="text" class="form-control" id="team_name" name="team_name" placeholder="Team Name" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success btn-block">Add Team</button>
            </div>
        </div>
    </form>

    <!-- PHP code to handle form submission and deletion -->
    <?php
    $conn = Database::getConnection();

    // Handle form submission for adding/updating teams
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['delete_team'])) {
            // Delete team logic
            $team_id = $_POST['delete_team'];
            $sql = "DELETE FROM teams WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $team_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Team deleted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        } elseif (isset($_POST['update_team'])) {
            // Update team logic
            $team_id = $_POST['team_id'];
            $team_name = $_POST['team_name'];
            $sql = "UPDATE teams SET team_name = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $team_name, $team_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Team updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        } else {
            // Insert new team
            $team_name = $_POST['team_name'];
            $sql = "INSERT INTO teams (team_name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $team_name);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Team added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        }
    }

    // Fetch the list of teams from the database
    $sql = "SELECT * FROM teams ORDER BY id DESC";
    $result = $conn->query($sql);
    ?>

    <!-- Display list of teams with Update and Delete buttons -->
    <h3 class="mt-5">List of Teams</h3>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($row['team_name']) ?></span>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="showUpdateModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['team_name']) ?>')">Update</button>
                        <form action="team.php" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_team" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item">No teams found.</li>
        <?php endif; ?>
    </ul>
</div>

<!-- Modal for updating the team name -->
<div class="modal fade" id="updateTeamModal" tabindex="-1" aria-labelledby="updateTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="team.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateTeamModalLabel">Update Team</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="team_id" name="team_id">
                    <div class="form-group">
                        <label for="team_name">Team Name</label>
                        <input type="text" class="form-control" id="modal_team_name" name="team_name" placeholder="Enter new team name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_team" class="btn btn-primary">Update Team</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Show the modal and populate the fields with the current team data
function showUpdateModal(id, name) {
    document.getElementById('team_id').value = id;
    document.getElementById('modal_team_name').value = name;
    $('#updateTeamModal').modal('show');
}

// JavaScript validation to ensure the team name is filled
document.getElementById('teamForm').addEventListener('submit', function(e) {
    let teamName = document.getElementById('team_name').value;
    if (!teamName) {
        alert('Please enter a team name!');
        e.preventDefault();
    }
});
</script>

<!-- Include Bootstrap's JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php Session::loadTemplate('footer'); ?>
