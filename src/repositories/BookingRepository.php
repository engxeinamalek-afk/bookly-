<?php
namespace App\repositories;
use mysqli;
class BookingRepository{
    private mysqli $conn;
    public function __construct(mysqli $conn)
    {
        $this->conn=$conn;
    }

    public function create($booking){
        $stmt= $this->conn->prepare("INSERT INTO bookings (user_id, start_time, end_time, date, status)
                                     VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $booking->user_id ,$booking->start_time ,$booking->end_time ,$booking->date ,$booking->status);
        $stmt->execute();
        return $this->conn->insert_id;
    }
}