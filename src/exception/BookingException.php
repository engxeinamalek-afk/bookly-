<?php
namespace App\exception;

use Exception;

class BookingException extends Exception{
    public static function notFound(int $bookingId): BookingException{
        return new BookingException("Booking with ID $bookingId not found.");
    }
    public static function notPending(int $bookingId): BookingException{
        return new BookingException("Booking with ID $bookingId isn't pending & cannot be canceled.");
    }
    public static function databaseError(): BookingException{
        return new BookingException("Technical error.");
    }
    public static function notAuthorized(): BookingException{
        return new BookingException("This booking is not yours.");
    }
}