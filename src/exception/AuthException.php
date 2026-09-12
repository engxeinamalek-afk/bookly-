<?php
namespace App\exception;

use Exception;

class AuthException extends Exception{
    public static function databaseError(): AuthException{
        return new AuthException("Technical error.");
    }
    public static function duplicateEmail():AuthException{
        return new AuthException("This email is already exists");
    }

    public static function invalidLoginInfo(): AuthException{
        return new AuthException("Invalid Email or Password");
    }
}