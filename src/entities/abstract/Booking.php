<?php
namespace App\entities\abstract;
use App\entities\enums\BookingStatus;
use App\entities\interface\Bookable;
abstract class Booking implements Bookable{
    public $id;
    public $user_id;
    public $start_time;
    public $end_time;
    public $date;
    public BookingStatus $status;
    public $type;
    abstract public function getDuration(): int;
    abstract public function getType(): string;
}