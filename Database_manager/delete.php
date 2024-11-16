<?php
include 'db_connect.php';
$table = $_GET['table'];
$key = $_GET['key'];
$id = $_GET['id'];

$sql = "DELETE FROM $table WHERE $key = '$id'";
if ($conn->query($sql)) {
    header("Location: table.php?table=$table&status=deleted");
} else {
    header("Location: table.php?table=$table&status=error");
}
exit();
?>
