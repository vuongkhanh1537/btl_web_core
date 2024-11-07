<?php
class UserModel {
    private $conn;
    private $customertablename = "customer";
    private $managertablename = "manager";

    public function __construct() {
        $this->conn = Database();
    }

    public function getCustomer($data) {
        try {
            $query = "SELECT * FROM  $this->customertablename  Where username=$data['username']";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new InternalServerError('Query Error !');
        }
        
    }

    public function createCustomer($data) {
        name=$data['name'];
        username=$data['username'];
        password=$data['password_'];
        gender=$data['gender'];
        birthday=$data['birthday'];
        email=$data['email'];

        try {
            $query = "INSERT INTO " . $this->customertablename . 
            "(`name_`, `password_`, `username`, `gender`, `birthday`, `email`) VALUES ('$name', '$password', '$username', '$gender', '$email',  '$birthday')"
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new InternalServerError('Query Error !');
        }
    }

    public function loginCustomer($username) {
        try {
            $query = "SELECT `id`, `name_`, `password_`, FROM ".  $this->customertablename . " WHERE username = '$username'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new InternalServerError('Query Error !');
        }
    }


    public function loginManager($id) {
        usermodel = UserModel();
        try {
            $query = "SELECT `id`, `name_`, `password_`, FROM ".  $this->managertablename . " WHERE username = '$username'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new InternalServerError('Query Error !');
        }
    }

}