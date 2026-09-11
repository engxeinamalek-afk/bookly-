<?php
namespace App\entities\enums;
enum BookingStatus: string{
    case PENDING = 'pending';
    case CONFIRMED = 'approved';
    case CANCELLED = 'rejected';
}