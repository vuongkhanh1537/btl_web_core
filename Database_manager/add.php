<?php
include 'db_connect.php';
$table = $_GET['table'];
$columns = $conn->query("SHOW COLUMNS FROM $table");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fields = [];
    $values = [];
    foreach ($_POST as $field => $value) {
        if (empty($value) && $field == $primaryKey) {
            // If the primary key (ID) is left blank, calculate max + 1
            $maxResult = $conn->query("SELECT MAX($primaryKey) as max_id FROM $table");
            $maxId = $maxResult->fetch_assoc()['max_id'] + 1;
            $values[] = "'" . $maxId . "'";
        } else {
            $fields[] = $field;
            $values[] = "'" . $conn->real_escape_string($value) . "'";
        }
    }
    $sql = "INSERT INTO $table (" . implode(",", $fields) . ") VALUES (" . implode(",", $values) . ")";
    if ($conn->query($sql)) {
        echo "<div class='alert alert-success'>Record added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Add Record</title>
</head>
<body>
<div class="container mt-5">
    <h2>Add Record to <?php echo $table; ?></h2>
    <a href="table.php?table=<?php echo $table; ?>" class="btn btn-secondary mb-3">Back to Table</a>
    <form method="post">
        <?php while ($column = $columns->fetch_assoc()) { ?>
            <div class="form-group">
                <label><?php echo $column['Field']; ?></label>
                <input type="text" name="<?php echo $column['Field']; ?>" class="form-control" <?php echo ($column['Field'] == $primaryKey) ? '' : 'required'; ?>>
                <?php if ($column['Field'] == $primaryKey) echo '<small>Leave blank to auto-increment.</small>'; ?>
            </div>
        <?php } ?>
        <button type="submit" class="btn btn-primary">Add Record</button>
    </form>
</div>
</body>
</html>
