<?php
class Database {
    private string $host = "localhost";
    private string $db_name = "booking_system_db2";
    private string $username = "root";
    private string $password = "";
    public ?mysqli $conn = null;

    public function connect(): ?mysqli {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        
        if ($this->conn->connect_error) {
            die("Connection Error: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8");
        return $this->conn;
    }
}
?>