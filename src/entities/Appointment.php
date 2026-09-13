<?php
namespace App\entities;
use App\entities\abstract\Booking;
class Appointment extends Booking{
    public function getDuration(): int{
        return 60;
    }
    public function getType(): string{
        return 'appointment';
    }
}