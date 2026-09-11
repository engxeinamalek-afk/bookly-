<?php
namespace App\repositories;

class UserRepository{
    private $conn;
    public function __construct($conn){
        $this->conn=$conn;
    }
    public function create($name,$email,$phone,$password){
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, phone, role, password) 
                                        VALUES (?, ?, ?, ?, ?)");
        $role="client";
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $name, $email, $phone, $role, $hashedPassword);
        if($stmt->execute()){
            return $this->conn->insert_id;
        }else{
            return false;
        }
    }
    public function checkEmail($email){
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    public function getUserByEmail($email){
        $stmt = $this->conn->prepare("SELECT id, name, email, phone, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}