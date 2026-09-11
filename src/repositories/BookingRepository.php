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
        $stmt= $this->conn->prepare("INSERT INTO bookings (user_id, start_time, end_time, date, status, type)
                                     VALUES (?, ?, ?, ?, ?, ?)");
        $status = $booking->status->value;
        $stmt->bind_param("isssss", $booking->user_id ,$booking->start_time ,$booking->end_time ,$booking->date ,$status, $booking->type);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function delete(int $bookingId, int $userId): bool{
            $stmt = $this->conn->prepare("DELETE FROM bookings
                                            WHERE id = ?
                                            AND user_id = ?
                                            AND status = 'pending'" );

        $stmt->bind_param("ii", $bookingId, $userId);

        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public function update($bookingId, $status){       
        $stmt = $this->conn->prepare("UPDATE bookings
                                        SET status = ?
                                        WHERE id = ?
                                        AND status = 'pending'");
        $stmt->bind_param("si", $status, $bookingId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getBookingsByUserId($userId){
        $stmt = $this->conn->prepare("SELECT date, start_time, end_time, type, status FROM bookings WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);// التحويل الى مصفوفة عناصرها مصفوفات كل key فيها هو اسم عمود
    }
}