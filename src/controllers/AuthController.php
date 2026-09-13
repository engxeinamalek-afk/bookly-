<?php
namespace App\controllers;

use App\exception\AuthException;
use mysqli;
use App\repositories\UserRepository;
use App\services\TokenService;
class AuthController{
    private mysqli $conn;
    public function __construct(mysqli $conn){
        $this->conn=$conn;
    }
    public function register()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $password = $data['password'] ?? null;
        if (!$name||!$email||!$password||!$phone) {
            return [
                'status' => 400,
                'success' => false,
                'message' => 'All fields are required'
            ];
        }

        // التأكد من عدم وجود الإيميل
        $repository = new UserRepository($this->conn);

        try{
            // إنشاء الحساب
            $userId = $repository->create($name, $email, $phone, $password);
            //تسجيل الدخول بعد انشاء الحساب
            $token = TokenService::createToken($this->conn, $userId);

            return [
                'status' => 201,
                'success' => true,
                'message' => 'Account created successfully',
                'user_id' => $this->conn->insert_id,
                'Token' => $token
            ];
        }catch(AuthException $e){
            return [
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            return [
                'status' => 400,
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }
    
        try{
                // البحث عن المستخدم
            $repository = new UserRepository($this->conn);
            $user = $repository->getUserByEmail($email);
            // التأكد من كلمة المرور
            if (!password_verify($password, $user['password']))// لانو مشفرة ما بقارن مباشرة
                throw AuthException::invalidLoginInfo();
            //انشاء التوكن
            $token = TokenService::createToken($this->conn, $user['id']);

            return [
                'status' => 200,
                'success' => true,
                'message' => 'Login successful',
                'Token' => $token
            ];
        }catch(AuthException $e){
            return [
                'status' => 401,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}