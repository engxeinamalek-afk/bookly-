<?php
namespace App\repositories;

use App\exception\ScheduleException;
use mysqli_sql_exception;

class ScheduleRepository{
    private $conn;
    public function __construct($conn)
    {
        $this->conn=$conn;
    }
    public function create($schedule){
        try{
            $stmt= $this->conn->prepare("INSERT INTO schedules (start_time, end_time, date)
                                        VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $schedule->start_time ,$schedule->end_time ,$schedule->date);
            $stmt->execute();
            if($stmt->affected_rows > 0){
                return $this->conn->insert_id;
            }
        }catch(mysqli_sql_exception $e){
            if($e->getCode() === 1062)
                throw ScheduleException::duplicateDate();
            throw ScheduleException::databaseError();
        }

    }
}