<?php
namespace App\services;
class ScheduleService{
    public static function isAvailable($conn, $startTime, $endTime, $date): bool{
        $stmt =$conn->prepare("SELECT COUNT(*) as total FROM schedules 
            WHERE DATE(date) = ? 
            AND ? >= start_time 
            AND ? <= end_time 
            LIMIT 1");
        $stmt->bind_param("sss", $date, $startTime , $endTime );
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
}