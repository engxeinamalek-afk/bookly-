<?php
namespace App\services;
use mysqli;
class TokenService{
    public static function createToken(mysqli $conn, int $userId): string{
        $token = bin2hex(random_bytes(32));
        $stmt = $conn->prepare("INSERT INTO user_tokens (user_id, token) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $token);
        $stmt->execute();
        return $token;
    }
}