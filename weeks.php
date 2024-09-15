<?php
include('libs/load.php');
Session::loadTemplate('head');
Session::loadTemplate('header');
?>


<div class="container mt-5">
    <h2 class="text-center">Weeks Management</h2>

    <!-- Form to add a new week -->
    <form action="weeks.php" class="mt-4" method="POST" id="weekForm">
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <input type="text" class="form-control" id="week_name" name="week_name" placeholder="Week Name (e.g., week_name)" required>
            </div>
            <div class="col-md-4 mb-3">
                <input type="date" class="form-control" id="from_date" name="from_date" required>
            </div>
            <div class="col-md-4 mb-3">
                <input type="date" class="form-control" id="to_date" name="to_date" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-block">Add Week</button>
    </form>

    <!-- PHP code to handle form submission, deletion, and updates -->
    <?php
    $conn = Database::getConnection();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['delete_week'])) {
            // Delete week logic and corresponding table
            $week_id = $_POST['delete_week'];
    
            $conn->begin_transaction();
    
            try {
                // Fetch the week name for the corresponding table
                $sql = "SELECT week_name FROM list_wee WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $week_id);
                $stmt->execute();
                $result = $stmt->get_result();
    
                // Check if week exists
                if ($result->num_rows > 0) {
                    $week = $result->fetch_assoc();
                    $table_name = 'week_table_' . $week['week_name']; // Use week name for table name
    
                    // Delete corresponding table
                    $conn->query("DROP TABLE IF EXISTS `$table_name`");
    
                    // Delete the week from the weeks table
                    $sql = "DELETE FROM list_wee WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $week_id);
                    $stmt->execute();
    
                    $conn->commit();
                    echo "<div class='alert alert-success'>Week and corresponding table deleted successfully!</div>";
                } else {
                    echo "<div class='alert alert-warning'>Week not found!</div>";
                }
            } catch (Exception $e) {
                $conn->rollback();
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
    
        } elseif (isset($_POST['update_week'])) {
            // Update week logic
            $week_id = $_POST['week_id'];
            $week_name = $_POST['week_name'];
            $from_date = $_POST['from_date'];
            $to_date = $_POST['to_date'];
    
            $sql = "UPDATE list_wee SET week_name = ?, from_date = ?, to_date = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $week_name, $from_date, $to_date, $week_id);
    
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Week updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
    
        } else {
            // Insert new week and create corresponding table
            $week_name = $_POST['week_name'];
            $from_date = $_POST['from_date'];
            $to_date = $_POST['to_date'];
    
            $conn->begin_transaction();
    
            try {
                // Insert new week into weeks table
                $sql = "INSERT INTO list_wee (week_name, from_date, to_date) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $week_name, $from_date, $to_date);
                $stmt->execute();
                $week_id = $conn->insert_id;
    
                // Create corresponding table for the week
                $table_name = 'week_table_' . $week_name;
                $create_table_sql = "
                    CREATE TABLE `$table_name` (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        emp_id INT NOT NULL,
                        team_id INT NOT NULL,
                        team_name VARCHAR(32),
                        employee_name VARCHAR(128),
                        sun VARCHAR(10),
                        mon VARCHAR(10),
                        tue VARCHAR(10),
                        wed VARCHAR(10),
                        thu VARCHAR(10),
                        fri VARCHAR(10),
                        sat VARCHAR(10)
                    )
                ";
                $conn->query($create_table_sql);
    
                $conn->commit();
                echo "<div class='alert alert-success'>Week added and corresponding table created successfully!</div>";
            } catch (Exception $e) {
                $conn->rollback();
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        }
    }
    

    // Fetch the list of weeks from the database
    $sql = "SELECT * FROM list_wee ORDER BY id DESC"; // Display in descending order so the latest one shows first
    $result = $conn->query($sql);
    ?>

    <!-- Display list of weeks with Update and Delete buttons -->
    <h3 class="mt-5">List of Weeks</h3>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($row['week_name']) . ' (From: ' . htmlspecialchars($row['from_date']) . ' To: ' . htmlspecialchars($row['to_date']) . ')' ?></span>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="showUpdateModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['week_name']) ?>', '<?= $row['from_date'] ?>', '<?= $row['to_date'] ?>')">Update</button>
                        <form action="weeks.php" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_week" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item">No weeks found.</li>
        <?php endif; ?>
    </ul>
</div>

<!-- Modal for updating the week -->
<div class="modal fade" id="updateWeekModal" tabindex="-1" aria-labelledby="updateWeekModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="weeks.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateWeekModalLabel">Update Week</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="week_id" name="week_id">
                    <div class="form-group">
                        <label for="week_name">Week Name</label>
                        <input type="text" class="form-control" id="modal_week_name" name="week_name" required>
                    </div>
                    <div class="form-group">
                        <label for="from_date">From Date</label>
                        <input type="date" class="form-control" id="modal_from_date" name="from_date" required>
                    </div>
                    <div class="form-group">
                        <label for="to_date">To Date</label>
                        <input type="date" class="form-control" id="modal_to_date" name="to_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_week" class="btn btn-primary">Update Week</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JavaScript to show the update modal and populate the fields with the current week data
function showUpdateModal(id, name, from_date, to_date) {
    document.getElementById('week_id').value = id;
    document.getElementById('modal_week_name').value = name;
    document.getElementById('modal_from_date').value = from_date;
    document.getElementById('modal_to_date').value = to_date;
    $('#updateWeekModal').modal('show');
}

// JavaScript validation to ensure all fields are filled
document.getElementById('weekForm').addEventListener('submit', function(e) {
    let weekName = document.getElementById('week_name').value;
    let fromDate = document.getElementById('from_date').value;
    let toDate = document.getElementById('to_date').value;
    if (!weekName || !fromDate || !toDate) {
        alert('Please fill all the fields!');
        e.preventDefault();
    }
});
</script>

<!-- Include Bootstrap's JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php Session::loadTemplate('footer'); ?>
