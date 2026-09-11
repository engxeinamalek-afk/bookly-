<?php
namespace App\Traits;
trait HasAuthorization {
    public function role($user_id , string $role) {
        $statement = $this->conn->prepare("SELECT * FROM users WHERE id = ? AND role = ?");
        $statement->bind_param("is", $user_id, $role);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            return true; 
        } else {
            return false;
        }
    }
    public function user(){
        $headers = getallheaders();
        $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '' );
        //هاد الاستعلام بيرجع المستخدم صاحب هاد التوكن
        $stmt = $this->conn->prepare( "SELECT u.* FROM users u JOIN user_tokens t ON t.user_id = u.id WHERE t.token = ?" );

        $stmt->bind_param("s", $token);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}