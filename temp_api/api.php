<?php
header("Content-Type: application/json");
require './db_connection.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$resource = array_shift($request); // Lấy bảng cần thao tác
$id = array_shift($request); // Lấy ID nếu có

// Xác định bảng và trường khóa chính
$table = '';
$primaryKey = '';

switch ($resource) {
    case 'customer':
        $table = 'customer';
        $primaryKey = 'customer_id';
        break;
    case 'manager':
        $table = 'manager';
        $primaryKey = 'empolyeeID';
        break;
    case 'product':
        $table = 'product';
        $primaryKey = 'product_id';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
        exit;
}

// Xử lý các phương thức HTTP
if ($method == 'GET' && !$id) {
    // Lấy tất cả các bản ghi trong bảng
    $stmt = $pdo->query("SELECT * FROM $table");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} elseif ($method == 'GET' && $id) {
    // Lấy một bản ghi theo ID
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $primaryKey = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($record) {
        echo json_encode($record);
    } else {
        http_response_code(404);
        echo json_encode(['error' => ucfirst($resource) . ' not found']);
    }
} elseif ($method == 'POST') {
    // Tạo mới một bản ghi
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Xây dựng các câu truy vấn dựa trên bảng
    if ($table == 'customer') {
        $stmt = $pdo->prepare("INSERT INTO customer (name_, password_, username, gender, birthday, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['password'], $data['username'], $data['gender'], $data['birthday'], $data['email']]);
    } elseif ($table == 'manager') {
        $stmt = $pdo->prepare("INSERT INTO manager (name_, password_, username, gender, birthday, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['password'], $data['username'], $data['gender'], $data['birthday'], $data['email']]);
    } elseif ($table == 'product') {
        $stmt = $pdo->prepare("INSERT INTO product (name_, price, color, descriptioin, weight, size, quantity, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['price'], $data['color'], $data['description'], $data['weight'], $data['size'], $data['quantity'], $data['category']]);
    }
    echo json_encode(['message' => ucfirst($resource) . ' created', 'id' => $pdo->lastInsertId()]);
} elseif ($method == 'PUT' && $id) {
    // Cập nhật bản ghi
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($table == 'customer') {
        $stmt = $pdo->prepare("UPDATE customer SET name_ = ?, password_ = ?, username = ?, gender = ?, birthday = ?, email = ? WHERE customer_id = ?");
        $stmt->execute([$data['name'], $data['password'], $data['username'], $data['gender'], $data['birthday'], $data['email'], $id]);
    } elseif ($table == 'manager') {
        $stmt = $pdo->prepare("UPDATE manager SET name_ = ?, password_ = ?, username = ?, gender = ?, birthday = ?, email = ? WHERE empolyeeID = ?");
        $stmt->execute([$data['name'], $data['password'], $data['username'], $data['gender'], $data['birthday'], $data['email'], $id]);
    } elseif ($table == 'product') {
        $stmt = $pdo->prepare("UPDATE product SET name_ = ?, price = ?, color = ?, descriptioin = ?, weight = ?, size = ?, quantity = ?, category = ? WHERE product_id = ?");
        $stmt->execute([$data['name'], $data['price'], $data['color'], $data['description'], $data['weight'], $data['size'], $data['quantity'], $data['category'], $id]);
    }
    echo json_encode(['message' => ucfirst($resource) . ' updated']);
} elseif ($method == 'DELETE' && $id) {
    // Xóa bản ghi theo ID
    $stmt = $pdo->prepare("DELETE FROM $table WHERE $primaryKey = ?");
    $stmt->execute([$id]);
    echo json_encode(['message' => ucfirst($resource) . ' deleted']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
