<?php

class UserModel {
    private $conn;
    private $tablename = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUser($data) {
        try {
            $username=$data['username'];
            $query = "SELECT * FROM  $this->tablename  Where username='$username'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new InternalServerError('Query Error !');
        }
        
    }

    public function createCustomer($data) {
        $name=$data['name'];
        $username=$data['username'];
        $password=$data['password'];
        $gender=$data['gender'];
        $birthday=$data['birthday'];
        $email=$data['email'];

        try {
            $query = "INSERT INTO " . $this->tablename . 
            "(`name_`, `password_`, role_, `username`, `gender`, `birthday`, `email`) VALUES ('$name', '$password', 'customer', '$username', '$gender', '$email',  '$birthday')";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new InternalServerError('Query Error !');
        }
    }

    // public function login($data) {
    //     try {
    //         $username =$data['username'];
    //         $query = "SELECT `user_id`, `name_`, `password_` FROM $this->tablename WHERE username = '$username'";
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->execute();
    //         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     } catch (PDOException $e) {
    //         echo $e->getMessage();
    //         throw new InternalServerError('Query Error !');
    //     }
    // }

}

?>