<?php
header("Content-Type: application/json");
require './db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$resource = array_shift($request); // Get the table to operate on
$id = array_shift($request); // Get the ID if provided

// Map table names to primary key columns
$tableMap = [
    'customer' => 'customer_id',
    'manager' => 'empolyeeID',
    'product' => 'product_id',
    'order_' => 'order_id',
    'promotion_code' => 'code_id',
    'cart' => 'cart_id',
    'create_' => ['cart_id', 'customer_id'],
    'make' => ['order_id', 'customer_id'],
    'apply_for' => ['order_id', 'promotion_code_id'],
    'contain' => ['order_id', 'product_id'],
    'consisted' => ['cart_id', 'product_id'],
    'rate' => ['customer_id', 'product_id'],
    'own' => ['customer_id', 'promotion_code_id'],
    'review' => ['product_id', 'ordinal_number']
];

// Determine the table and primary key(s)
$table = $resource;
$primaryKey = $tableMap[$resource];
if (is_array($primaryKey)) {
    $primaryKeyCount = count($primaryKey);
} else {
    $primaryKeyCount = 1;
}

// Handle HTTP methods
if ($method == 'GET' && !$id) {
    // Get all records from the table
    $stmt = $pdo->query("SELECT * FROM $table");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} elseif ($method == 'GET' && $id) {
    // Get a single record by ID
    if ($primaryKeyCount == 1) {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $primaryKey = ?");
        $stmt->execute([$id]);
    } else {
        $idParts = explode(',', $id);
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE " . implode(' = ? AND ', $primaryKey) . " = ?");
        $stmt->execute(array_merge($idParts, $idParts));
    }
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($record) {
        echo json_encode($record);
    } else {
        http_response_code(404);
        echo json_encode(['error' => ucfirst($resource) . ' not found']);
    }
} elseif ($method == 'POST') {
    // Create a new record
    $data = json_decode(file_get_contents("php://input"), true);

    $columns = array_keys($data);
    $values = array_values($data);

    $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . str_repeat('?, ', count($columns) - 1) . '?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    echo json_encode(['message' => ucfirst($resource) . ' created', 'id' => $pdo->lastInsertId()]);
} elseif ($method == 'PUT' && $id) {
    // Update a record
    $data = json_decode(file_get_contents("php://input"), true);

    $sets = [];
    $values = [];
    foreach ($data as $column => $value) {
        $sets[] = "$column = ?";
        $values[] = $value;
    }
    if ($primaryKeyCount == 1) {
        $values[] = $id;
        $sql = "UPDATE $table SET " . implode(', ', $sets) . " WHERE $primaryKey = ?";
    } else {
        $idParts = explode(',', $id);
        $values = array_merge($values, $idParts);
        $sql = "UPDATE $table SET " . implode(', ', $sets) . " WHERE " . implode(' = ? AND ', $primaryKey) . " = ?";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    echo json_encode(['message' => ucfirst($resource) . ' updated']);
} elseif ($method == 'DELETE' && $id) {
    // Delete a record
    if ($primaryKeyCount == 1) {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE $primaryKey = ?");
        $stmt->execute([$id]);
    } else {
        $idParts = explode(',', $id);
        $sql = "DELETE FROM $table WHERE " . implode(' = ? AND ', $primaryKey) . " = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($idParts);
    }
    echo json_encode(['message' => ucfirst($resource) . ' deleted']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>