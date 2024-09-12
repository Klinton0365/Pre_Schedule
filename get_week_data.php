<?php
include('libs/load.php');
$conn = Database::getConnection();

if (isset($_GET['week_id'])) {
    $week_id = $_GET['week_id'];

    // Get the table name for the selected week
    $week_table_sql = "SELECT week_name FROM list_wee WHERE id = ?";
    $stmt = $conn->prepare($week_table_sql);
    $stmt->bind_param('i', $week_id);
    $stmt->execute();
    $week_table_result = $stmt->get_result();
    $week_row = $week_table_result->fetch_assoc();
    $table_name = "week_table_" . $week_row['week_name'];

    // Fetch data from the selected week's table
    $sql = "SELECT emp_id, team_id, team_name, sun, mon, tue, wed, thu, fri, sat FROM `$table_name`";
    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Fetch employee name from the employees table
        $emp_sql = "SELECT employee_name FROM employees WHERE id = ?";
        $emp_stmt = $conn->prepare($emp_sql);
        $emp_stmt->bind_param('i', $row['emp_id']);
        $emp_stmt->execute();
        $emp_result = $emp_stmt->get_result();
        $emp_row = $emp_result->fetch_assoc();

        // Add employee name to the row
        $row['employee_name'] = $emp_row['employee_name'];

        $data[] = $row;
    }

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
}
?>
