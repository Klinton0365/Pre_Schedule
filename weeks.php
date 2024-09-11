<?php include('_templates/head.php'); ?>
<?php include('_templates/header.php'); ?>
<?php include('libs/load.php'); ?> <!-- Include your database connection -->

<div class="container mt-5">
    <h2 class="text-center">Weeks Management</h2>

    <!-- Form to add a new week -->
    <form action="weeks.php" class="mt-4" method="POST" id="weekForm">
        <div class="form-row">
            <div class="col-md-4 mb-3">
                <input type="text" class="form-control" id="week_name" name="week_name" placeholder="Week Name" required>
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

    <!-- PHP code to handle form submission -->
    <?php
    $conn = Database::getConnection();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get form data
        $week_name = $_POST['week_name'];
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];

        // Insert data into the database
        $sql = "INSERT INTO weeks (week_name, from_date, to_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $week_name, $from_date, $to_date);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Week added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }

    // Fetch the list of weeks from the database
    $sql = "SELECT * FROM weeks ORDER BY id DESC"; // Display in descending order so the latest one shows first
    $result = $conn->query($sql);
    ?>

    <!-- Display list of weeks -->
    <h3 class="mt-5">List of Weeks</h3>
    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($row['week_name']) . ' (From: ' . htmlspecialchars($row['from_date']) . ' To: ' . htmlspecialchars($row['to_date']) . ')' ?>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item">No weeks found.</li>
        <?php endif; ?>
    </ul>
</div>

<script>
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

<?php include('_templates/footer.php'); ?>
