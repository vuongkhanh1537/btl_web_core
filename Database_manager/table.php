<?php
include 'db_connect.php';
$table = $_GET['table'];

// Notification Messages
$message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'deleted') {
        $message = "<div class='alert alert-success'>Record deleted successfully!</div>";
    } elseif ($_GET['status'] == 'error') {
        $message = "<div class='alert alert-danger'>An error occurred. Please try again.</div>";
    }
}

// Fetch primary key and table fields
$primaryKeyResult = $conn->query("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
$primaryKey = $primaryKeyResult->fetch_assoc()['Column_name'];
$fields = $conn->query("SHOW COLUMNS FROM $table");

// Search functionality
$searchQuery = "";
if (isset($_POST['search']) && isset($_POST['field'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $field = $conn->real_escape_string($_POST['field']);
    $searchQuery = "WHERE $field LIKE '%$search%'";
}
$result = $conn->query("SELECT * FROM $table $searchQuery");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Manage Table</title>
    <script>
        function confirmDelete(table, key, id) {
            if (confirm("Are you sure you want to delete this record?")) {
                window.location.href = `delete.php?table=${table}&key=${key}&id=${id}`;
            }
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Table: <?php echo $table; ?></h2>
    <?php echo $message; ?>

    <!-- Search Form -->
    <form method="post" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <select name="field" class="form-control" required>
                    <option value="">Select Field</option>
                    <?php while ($field = $fields->fetch_assoc()) { ?>
                        <option value="<?php echo $field['Field']; ?>"><?php echo $field['Field']; ?></option>
                    <?php } $fields->data_seek(0); ?>
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="search" placeholder="Search..." class="form-control" value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>
    
    <a href="index.php" class="btn btn-secondary mb-3">Back to Table Selection</a>
    <a href="add.php?table=<?php echo $table; ?>" class="btn btn-primary mb-3">Add Record</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <?php while ($field = $fields->fetch_assoc()) { echo "<th>" . $field['Field'] . "</th>"; } ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <?php foreach ($row as $data) { echo "<td>" . htmlspecialchars($data) . "</td>"; } ?>
                    <td>
                        <a href="edit.php?table=<?php echo $table; ?>&key=<?php echo $primaryKey; ?>&id=<?php echo $row[$primaryKey]; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="#" onclick="confirmDelete('<?php echo $table; ?>', '<?php echo $primaryKey; ?>', '<?php echo $row[$primaryKey]; ?>')" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
