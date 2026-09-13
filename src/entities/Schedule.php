<?php
namespace App\entities;
use App\entities\trait\HasSchedule;
class Schedule{
    public $id;
    use HasSchedule;
}