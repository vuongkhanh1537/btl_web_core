<?php
include 'db_connect.php';
$table = $_GET['table'];
$id = $_GET['id'];
$key = $_GET['key'];
$record = $conn->query("SELECT * FROM $table WHERE $key = '$id'")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updates = [];
    foreach ($_POST as $field => $value) {
        $updates[] = "$field='" . $conn->real_escape_string($value) . "'";
    }
    $sql = "UPDATE $table SET " . implode(",", $updates) . " WHERE $key = '$id'";
    if ($conn->query($sql)) {
        echo "<div class='alert alert-success'>Record updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Edit Record</title>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Record in <?php echo $table; ?></h2>
    <a href="table.php?table=<?php echo $table; ?>" class="btn btn-secondary mb-3">Back to Table</a>
    <form method="post">
        <?php foreach ($record as $field => $value) { ?>
            <div class="form-group">
                <label><?php echo $field; ?></label>
                <input type="text" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($value); ?>" class="form-control">
            </div>
        <?php } ?>
        <button type="submit" class="btn btn-primary">Update Record</button>
    </form>
</div>
</body>
</html>
