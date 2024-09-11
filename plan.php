<?php include('_templates/head.php'); ?>
<?php include('_templates/header.php'); ?>
<div class="container mt-5">
    <h2 class="text-center">Plan for the Week</h2>
    <form class="mt-4" method="POST" id="planForm">
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <select class="form-control" id="week" name="week" required>
                    <option value="">Select Week</option>
                    <?php foreach ($weeks as $week): ?>
                        <option value="<?= htmlspecialchars($week) ?>"><?= htmlspecialchars($week) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select class="form-control" id="team" name="team" required>
                    <option value="">Select Team</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= htmlspecialchars($team) ?>"><?= htmlspecialchars($team) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select class="form-control" id="employee" name="employee" required>
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= htmlspecialchars($employee) ?>"><?= htmlspecialchars($employee) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <input type="number" class="form-control" id="hours" name="hours" placeholder="Hours" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-block">Add Plan</button>
    </form>

    <h3 class="mt-5">Consolidated Plans</h3>
    <ul class="list-group">
        <?php foreach ($plans as $plan): ?>
            <li class="list-group-item"><?= htmlspecialchars($plan) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
document.getElementById('planForm').addEventListener('submit', function(e) {
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
