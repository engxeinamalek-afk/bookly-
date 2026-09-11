<?php
namespace App\controllers;
use mysqli;
use App\repositories\UserRepository;
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
        if ($repository->checkEmail($email)) {
            return [
                'status' => 409,
                'success' => false,
                'message' => 'Email already exists'
            ];
        }

        // إنشاء الحساب
        $userId = $repository->create($name, $email, $phone, $password);
        //تسجيل الدخول بعد انشاء الحساب
        return [
            'status' => 201,
            'success' => true,
            'message' => 'Account created successfully',
            'user_id' => $this->conn->insert_id
        ];
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

        // البحث عن المستخدم
        $repository = new UserRepository($this->conn);
        $user = $repository->getUserByEmail($email);
        if($user === null){
            return [
                'status' => 401,
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        // التأكد من كلمة المرور
        if (!password_verify($password, $user['password'])) {// لانو مشفرة ما بقارن مباشرة
            return [
                'status' => 401,
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        //انشاء التوكن



        return [
            'status' => 200,
            'success' => true,
            'message' => 'Login successful'
        ];
    }
}