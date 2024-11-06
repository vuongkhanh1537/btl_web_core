<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Database Management</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Select a Table</h1>
        <ul class="list-group">
            <?php
            $tables = $conn->query("SHOW TABLES");
            while ($table = $tables->fetch_array()) {
                echo "<li class='list-group-item'><a href='table.php?table=" . $table[0] . "'>" . $table[0] . "</a></li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
