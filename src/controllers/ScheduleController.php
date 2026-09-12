<?php
namespace App\controllers;
use mysqli;
use App\Traits\HasAuthorization;
use App\entities\Schedule;
use App\exception\ScheduleException;
use App\repositories\ScheduleRepository;
class ScheduleController {
    use HasAuthorization;
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    
    public function store(){
        //الصلاحية
        $id=$this->user()['id'];
        if(!$this->user() || !$this->role($id , 'admin')){
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }
        //جلب البيانات 
        $data = json_decode(file_get_contents("php://input"), true);
        //هون لازم اعمل فاليديت للداتا
        //انشاء كائن
        $schedule = new Schedule();
        $schedule->start_time = $data['start_time'];
        $schedule->end_time = $data['end_time'];
        $schedule->date = $data['date'];
        //تمرير الكائن الى الريبو
        $repo= new ScheduleRepository($this->conn);
        try{
            $id=$repo->create($schedule);
            return [
                'status' => 200,
                'success' => true,
                'id' => $id
            ];
        } catch (ScheduleException $e) {
            return [
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }

}
