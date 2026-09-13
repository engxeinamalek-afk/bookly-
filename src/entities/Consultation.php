<?php
namespace App\entities;
use App\entities\abstract\Booking;
class Consultation extends Booking{
    public function getDuration(): int{
        return 30;
    }
    public function getType(): string{
        return 'consultation';
    }
}