<?php
namespace App\repositories;

class ScheduleRepository{
    private $conn;
    public function __construct($conn)
    {
        $this->conn=$conn;
    }
    public function create($schedule){
        $stmt= $this->conn->prepare("INSERT INTO schedules (start_time, end_time, date)
                                     VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $schedule->start_time ,$schedule->end_time ,$schedule->date);
        $stmt->execute();
        return $this->conn->insert_id;
    }
}