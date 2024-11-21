<?php
header("Content-Type: application/json");
require './db_connection.php';
require_once '../config/Authorization.php';

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

// Khởi tạo đối tượng Authorization
$auth = new Authorization();

// Quy định quyền hạn cho từng bảng và vai trò
$permissions = [
    'customer' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['read', 'update']
    ],
    'manager' => [
        'admin' => ['create', 'read', 'update', 'delete']
    ],
    'product' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['read']
    ],
    'order_' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['create', 'read', 'update', 'delete']
    ],
    'promotion_code' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['read']
    ],
    'cart' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['create', 'read', 'update', 'delete']
    ],
    'contain' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['read']
    ],
    'rate' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['create', 'read', 'update', 'delete']
    ],
    'review' => [
        'admin' => ['create', 'read', 'update', 'delete'],
        'user' => ['read']
    ]
];

// Cấu hình phân quyền
function checkRolePermission($table, $method, $userRole, $permissions) {
    // Kiểm tra xem bảng và vai trò có trong cấu hình không
    if (isset($permissions[$table][$userRole]) && in_array($method, $permissions[$table][$userRole])) {
        return true;
    }
    return false;
}

// Hàm kiểm tra JWT và vai trò người dùng
function checkUserRole($table, $method, $permissions, $auth) {
    // Lấy token từ Header Authorization
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["message" => "Authorization token missing"]);
        exit;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    // Giải mã token và lấy role từ payload
    try {
        $payload = $auth->decode($token);
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => $e->getMessage()]);
        exit;
    }

    // Lấy vai trò từ payload
    $userRole = $payload['role'] ?? null;
    if (!$userRole || !checkRolePermission($table, strtolower($method), $userRole, $permissions)) {
        http_response_code(403); // Forbidden
        echo json_encode(["message" => "You do not have permission to access this resource"]);
        exit;
    }
}

// Kiểm tra quyền hạn trước khi xử lý API
checkUserRole($table, strtolower($method), $permissions, $auth);


// Handle HTTP methods
if ($method == 'GET' && $id && $resource == 'order_') {
    // Get a single order and the associated customer information
    $stmt = $pdo->prepare("
        SELECT 
            o.*, 
            c.name_, 
            c.email
        FROM order_ o
        JOIN customer c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($order) {
        echo json_encode($order);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
    }
} elseif ($method == 'GET' && !$id) {
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