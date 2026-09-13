<?php
namespace App\entities\interface;

interface Bookable {
    public function getDuration():int;
    public function gettype(): string;
}