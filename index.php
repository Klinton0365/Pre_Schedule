<?php
include('libs/load.php');
Session::loadTemplate('head');
Session::loadTemplate('header');
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Weekly Pre Scheduling</h1>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <a href="weeks.php" class="btn btn-primary btn-block mb-3">Manage Weeks</a>
        </div>
        <div class="col-md-4">
            <a href="team.php" class="btn btn-primary btn-block mb-3">Manage Teams</a>
        </div>
        <div class="col-md-4">
            <a href="employee.php" class="btn btn-primary btn-block mb-3">Manage Employees</a>
        </div>
        <div class="col-md-4">
            <a href="plan.php" class="btn btn-primary btn-block mb-3">Plan for the Week</a>
        </div>
    </div>
</div>
<?php Session::loadTemplate('footer'); ?>
