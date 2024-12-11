<?php

class UserModel {
    private $conn;
    private $tableName = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllCustomers() {
        $query = "SELECT * FROM " . $this->tableName . " WHERE role_ = 'customer'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->tableName;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->tableName . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validateAndCreate($data) {
        if(!$this->validateData($data, true))
            throw new Exception("Invalid data");


        $existing = $this->getByEmail($data['email']);
        if (count($existing) >= 1) {
            throw new Exception('email already exists');
        }

        $query = "INSERT INTO " . $this->tableName . " 
            (name_, password_, role_,  gender, birthday, email) 
            VALUES (:name, :password, :role,  :gender, :birthday, :email)";

        $stmt = $this->conn->prepare($query);
        $this->bindUserParams($stmt, $data);
        $stmt->execute();
        $user_id = $this->conn->lastInsertId();
        return $this->getById($user_id);
    }

    public function validateAndUpdate($user_id,$data) {
        // Check if user exists
        $existing = $this->getById($user_id);
        if (!$existing) {
            throw new Exception('User not found');
        }

        // Validate provided fields
        if (!$this->validateData($data, false)) {
            throw new Exception('Invalid data');
        }

        $query = "UPDATE " . $this->tableName . " SET 
            name_ = :name,
            gender = :gender,
            birthday = :birthday,
            password_ = :password
            WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":gender", $data['gender']);
        $stmt->bindParam(":birthday", $data['birthday']);
        $stmt->bindParam(":password", $data['password']);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function delete($id) {
        if (!$this->getById($id)) {
            throw new Exception('User not found');
        }
        $query = "DELETE FROM " . $this->tableName . " WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function validateLogin($data) {
        if (!isset($data['email']) || !isset($data['password'])) {
            throw new Exception('Email and password are required');
        }
        
        $users = $this->getByEmail($data['email']);
        if (count($users) === 0) {
            throw new Exception('Email not found');
        }
        $user = $users[0];
        if (!password_verify($data['password'], $user['password_'])) {
            throw new Exception('Invalid password');
        }

        return $user;
    }

    private function validateData($data, $isCreate) {
        $rules = [
            'name' => 'required|max',
            'gender' => 'required|gender',
            'birthday' => 'required|date',
            'email' => 'required|email'
        ];

        if ($isCreate) {
            $rules['password'] = 'required|min|max';
        }

        return Validator::validate($data, array_intersect_key($rules, $data));
    }

    private function bindUserParams($stmt, $data) {
        // Create variables to hold the values
        $name = $data['name'] ?? '';
        $gender = $data['gender'] ?? '';
        $birthday = $data['birthday'] ?? '';
        $email = $data['email'] ?? '';

        // Bind using variables
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":birthday", $birthday);
        $stmt->bindParam(":email", $email);
        if (isset($data['password'])) {
            $password = $data['password'];
            $role = $data['role'] ?? 'customer'; // Default to customer
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":role", $role);
        }
    }
}
?>