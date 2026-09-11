<?php
namespace App\entities;
use App\entities\abstract\Booking;
class Appointment extends Booking{
    public function getDuration(){
        return 60;
    }
    public function getType(){
        return 'appointment';
    }
}