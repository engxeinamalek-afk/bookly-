<?php
namespace App\services;

use mysqli;

class AuthService{
    public static function user(mysqli $conn){
        $headers = getallheaders();
        $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '' );
        //هاد الاستعلام بيرجع المستخدم صاحب هاد التوكن
        $stmt = $conn->prepare( "SELECT u.* FROM users u JOIN user_tokens t ON t.user_id = u.id WHERE t.token = ?" );

        $stmt->bind_param("s", $token);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}