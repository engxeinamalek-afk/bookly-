<?php
namespace App\entities\abstract;
use App\entities\enums\BookingStatus;
use App\entities\interface\Bookable;
use App\entities\trait\HasSchedule;
abstract class Booking implements Bookable{
    public $id;
    public $user_id;
    use HasSchedule;
    public BookingStatus $status;
    public $type;
    abstract public function getDuration(): int;
    abstract public function getType(): string;
}