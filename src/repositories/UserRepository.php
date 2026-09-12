<?php
namespace App\repositories;

use App\exception\AuthException;

class UserRepository{
    private $conn;
    public function __construct($conn){
        $this->conn=$conn;
    }
    public function create($name,$email,$phone,$password){
        try{
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, phone, role, password) 
                                            VALUES (?, ?, ?, ?, ?)");
            $role="client";
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssss", $name, $email, $phone, $role, $hashedPassword);
            if($stmt->execute())
                return $this->conn->insert_id;
        }catch(\mysqli_sql_exception $e){
            if($e->getCode() === 1062)//هاد كود اخطاء القيم المكررة يلي المفروض تكون يونيك
                throw AuthException::duplicateEmail();
            throw AuthException::databaseError();
        }

    }

    public function getUserByEmail($email){
        try{
            $stmt = $this->conn->prepare("SELECT id, name, email, phone, role, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user= $result->fetch_assoc();
            if($user == null)
                throw AuthException::invalidLoginInfo();
            return $user;
        }catch(\mysqli_sql_exception $e){
            throw AuthException::databaseError();
        }
    }
}