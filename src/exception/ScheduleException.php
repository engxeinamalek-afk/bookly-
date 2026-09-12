<?php
namespace App\exception;

use Exception;

class ScheduleException extends Exception{
    public static function databaseError(): ScheduleException{
        return new ScheduleException("Technical error.");
    }
    public static function duplicateDate():ScheduleException{
        return new ScheduleException("This date is already scheduled");
    }
}