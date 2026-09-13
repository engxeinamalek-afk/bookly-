<?php
namespace App\controllers;
use mysqli;
use App\repositories\BookingRepository;
use App\entities\Appointment;
use App\entities\Consultation;
use App\entities\enums\BookingStatus;
use App\exception\BookingException;
use App\services\AuthService;
use App\services\ConflictService;
use App\services\ScheduleService;
class BookingController{
    private mysqli $conn;
    public function __construct(mysqli $conn){
        $this->conn=$conn;
    }
    public function book(){
        $user=AuthService::user($this->conn);
        if(!$user || $user['role'] !== 'client'){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $id= $user['id'];
        $data = json_decode(file_get_contents("php://input"), true);
        
        // هون لازم حط فاليديت للداتا
        if ( empty($data['type']) || empty($data['date']) || empty($data['start_time'])) {
            return [
                'status' => 400,
                'success' => false,
                'message' => 'Type, date and start time are required'
            ];
        }        
        if($data['type'] === 'appointment')
            $booking = new Appointment();
        else if($data['type'] === 'consultation')
            $booking = new Consultation();
        else
            return [
                'success' => false,
                'message' => 'Invalid booking type'
            ];

        $end_time = date('H:i:s', strtotime($data['start_time']) + $booking->getDuration() * 60);

        $booking->user_id = $id;
        $booking->date = $data['date'];
        $booking->start_time = $data['start_time'];
        $booking->end_time = $end_time;
        $booking->status = BookingStatus::PENDING;
        $booking->type = $booking->getType();
        //هون لازم حط الاختبار اذا ممكن الحجز isAvailable?
        if(ConflictService::isAvailable($this->conn, $data['date'], $data['start_time'], $end_time)
            && ScheduleService::isAvailable($this->conn, $data['start_time'], $end_time, $data['date'])){
            try{
                $repository = new BookingRepository($this->conn);
                $id = $repository->create($booking);
                return [
                    'success' => true,
                    'id' => $id
                ];
            }catch(BookingException $e){
                return [
                    'status' => 400,
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }

        }else{
            return [
                'success' => false,
                'message' => 'The selected time slot is not available'
            ];
        }
 
    }

    public function cancel(int $booking_id)   {
        $user=AuthService::user($this->conn);
        if(!$user || $user['role'] !== 'client'){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $id= $user['id'];
        try{
            $repository = new BookingRepository($this->conn);
            $canceled = $repository->delete( $booking_id, $id );
            return [
                "status" => 200,
                'success' => true,
                'message' => 'Booking canceled successfully'
            ];
        } catch (BookingException $e) {
            return [
                "status" => 400,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function setStatus($bookingId){
        //التحقق من الصاحية والطبيب
        $user=AuthService::user($this->conn);
        if(!$user || $user['role'] !== 'admin'){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $status = $data['status'];
        if(!in_array($status, [BookingStatus::CONFIRMED->value, BookingStatus::CANCELLED->value])){
            return [
                'success' => false,
                'message' => 'Invalid status'
            ];
        }
        try{
            $repo= new BookingRepository($this->conn);
            if($repo->update($bookingId, $status))
                return [
                    "status" => 200,
                    'success' => true,
                    'message' => 'Booking status updated successfully'
                ];      
        }catch(BookingException $e){
            return [
                "status" => 400,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getApprovedBookings(){
        $user=AuthService::user($this->conn);
        if(!$user || $user['role'] !== 'client'){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $id= $user['id'];
        try{
            $repo= new BookingRepository($this->conn);
            $bookings = $repo->getBookingsByUserId($id);
            $bookings = array_filter($bookings, function($booking) {
                return $booking['status'] === BookingStatus::CONFIRMED->value;
            });
            if(empty($bookings)){
                return [
                    'success' => true,
                    'message' => "There is no approved bookings for this user",
                ];
            }
            return [
                'success' => true,
                'bookings' => array_values($bookings)
            ];
        }catch(BookingException $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }
    public function getRejectedBookings(){
        $user=AuthService::user($this->conn);
        if(!$user || $user['role'] !== 'client'){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $id= $user['id'];
        try{
            $repo= new BookingRepository($this->conn);
            $bookings = $repo->getBookingsByUserId($id);
            $bookings = array_filter($bookings, function($booking) {
                return $booking['status'] === BookingStatus::CANCELLED->value;
            });
            if(empty($bookings)){
                return [
                    'success' => true,
                    'message' => "There is no rejected bookings for this user",
                ];
            }
            return [
                'success' => true,
                'bookings' => array_values($bookings)
            ];
        }catch(BookingException $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }
}