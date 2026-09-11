<?php
namespace App\controllers;
use mysqli;
use App\repositories\BookingRepository;
use App\entities\Appointment;
use App\entities\Consultation;
use App\entities\enums\BookingStatus;
use App\services\ConflictService;
use App\services\ScheduleService;
use App\Traits\HasAuthorization;
class BookingController{
    use HasAuthorization;
    private mysqli $conn;
    public function __construct(mysqli $conn){
        $this->conn=$conn;
    }
    public function book(){
        $id=$this->user()['id'];
        if(!$this->user() || !$this->role($id , 'client')){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $data = json_decode(file_get_contents("php://input"), true);

        // هون لازم حط فاليديت للداتا
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
            $repository = new BookingRepository($this->conn);

            $id = $repository->create($booking);

            return [
                'success' => true,
                'id' => $id
            ];
        }else{
            return [
                'success' => false,
                'message' => 'The selected time slot is not available'
            ];
        }
 
    }

    public function cancel(int $booking_id)   {
        $id=$this->user()['id'];
        if(!$this->user() || !$this->role($id , 'client')){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $repository = new BookingRepository($this->conn);

        $canceled = $repository->delete( $booking_id, $id );

        if (!$canceled) {
            return [
                'success' => false,
                'message' => 'Booking not found or you do not own this booking'
            ];
        }

        return [
            'success' => true,
            'message' => 'Booking canceled successfully'
        ];
    }

    public function setStatus($bookingId){
        //التحقق من الصاحية والطبيب
        $id=$this->user()['id'];
        if(!$this->user() || !$this->role($id , 'admin')){
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
        $repo= new BookingRepository($this->conn);
        if($repo->update($bookingId, $status)){
            return [
                'success' => true,
                'message' => 'Booking status updated successfully'
            ];
        }else{
            return [
                'success' => false,
                'message' => 'Booking not found or status is not pending'
            ];
        }  
    }

    public function getApprovedBookings(){
        $id=$this->user()['id'];
        if(!$this->user() || !$this->role($id , 'client')){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $repo= new BookingRepository($this->conn);
        $bookings = $repo->getBookingsByUserId($id);
        $bookings = array_filter($bookings, function($booking) {
            return $booking['status'] === BookingStatus::CONFIRMED->value;
        });
        return [
            'success' => true,
            'bookings' => array_values($bookings)
        ];
    }
    public function getRejectedBookings(){
        $id=$this->user()['id'];
        if(!$this->user() || !$this->role($id , 'client')){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        $repo= new BookingRepository($this->conn);
        $bookings = $repo->getBookingsByUserId($id);
        $bookings = array_filter($bookings, function($booking) {
            return $booking['status'] === BookingStatus::CANCELLED->value;
        });
        return [
            'success' => true,
            'bookings' => array_values($bookings)
        ];
    }
}