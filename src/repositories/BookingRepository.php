<?php
namespace App\repositories;

use App\exception\BookingException;
use mysqli;
use \mysqli_sql_exception;
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

    public function delete(int $bookingId, int $userId){
        try{
            $stmt = $this->conn->prepare("DELETE FROM bookings
                                            WHERE id = ?
                                            AND user_id = ?
                                            AND status = 'pending'" );

            $stmt->bind_param("ii", $bookingId, $userId);
            $stmt->execute();
            if($stmt->affected_rows > 0){
                return true;
            }
            $stmt= $this->conn->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows === 0){//اذا مافي حجز بهالid
                throw BookingException::notFound($bookingId);
            }
            $result = $result->fetch_assoc();
            $status = $result['user_id'];
            if($status !== $userId){//اذا في حجز بس مانو لهالمستخدم
                throw BookingException::notAuthorized();
            }
            if($result['status'] !== 'pending'){//اذا في حجز بس مانو قيد الانتظار
                throw BookingException::notPending($bookingId);
            }
        } catch (\mysqli_sql_exception $e) {
            throw BookingException::databaseError();
        }
    }

    public function update($bookingId, $status){
        try{
            $stmt = $this->conn->prepare("UPDATE bookings
                                            SET status = ?
                                            WHERE id = ?
                                            AND status = 'pending'");
            $stmt->bind_param("si", $status, $bookingId);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {//تم تعديل السجل بنجاح
                return true;
            }
            $stmt= $this->conn->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows === 0){//اذا مافي حجز بهالid
                throw BookingException::notFound($bookingId);
            }
            $result = $result->fetch_assoc();
            $status = $result['status'];
            if($status !== 'pending'){//اذا في حجز بس مانو قيد الانتظار
                throw BookingException::notPending($bookingId);
            }
            return false;
        } catch (\mysqli_sql_exception $e) {//اي خطأ ممكن تاني ممكن يصير بالداتابيز
            throw BookingException::databaseError();
        }

    }

    public function getBookingsByUserId($userId){
        $stmt = $this->conn->prepare("SELECT date, start_time, end_time, type, status FROM bookings WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);// التحويل الى مصفوفة عناصرها مصفوفات كل key فيها هو اسم عمود
    }
}