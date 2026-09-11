<?php
namespace App\entities\abstract;
use App\entities\enums\BookingStatus;
abstract class Booking{
    public $id;
    public $user_id;
    public $start_time;
    public $end_time;
    public $date;
    public BookingStatus $status;
    public $type;
    abstract public function getDuration();
    abstract public function getType();
}