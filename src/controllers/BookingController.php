<?php
namespace App\controllers;
use mysqli;
use App\repositories\BookingRepository;
use App\entities\Appointment;
use App\entities\Consultation;
use App\entities\enums\BookingStatus;
class BookingController{
    private mysqli $conn;
    public function __construct(mysqli $conn){
        $this->conn=$conn;
    }
    public function book(){
        //هون لازم اعمل اختبار للصلاحية
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
        $booking->user_id = $data['user_id'];
        $booking->date = $data['date'];
        $booking->start_time = $data['start_time'];
        $booking->end_time = $end_time;
        $booking->status = BookingStatus::PENDING;
        $booking->type = $data['type'];
        //هون لازم حط الاختبار اذا ممكن الحجز isAvailable?
        $repository = new BookingRepository($this->conn);

        $id = $repository->create($booking);

        return [
            'success' => true,
            'id' => $id
        ];

 
    }
}