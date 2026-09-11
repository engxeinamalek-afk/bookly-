<?php
namespace App\services;
class ConflictService {
    public static function isAvailable($conn, $date, $start_time, $end_time) {
        $stmt =$conn->prepare("SELECT COUNT(*) as total FROM bookings
                WHERE date = ?
                AND status IN ('pending' , 'approved') 
                AND (? < end_time AND ? > start_time)");
        $stmt->bind_param("sss", $date, $start_time , $end_time );
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] === 0;
        
    }
}