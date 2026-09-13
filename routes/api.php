<?php
use App\controllers\AuthController;
use App\controllers\BookingController;
use App\controllers\ScheduleController;
return [
    'POST' => [
         '/register' => AuthController::class.'@register',
         '/login' => AuthController::class.'@login',
         '/book' => BookingController::class.'@book',
         '/setSchedule' => ScheduleController::class.'@store',
         '/setStatus/{id}' => BookingController::class.'@setStatus'
    ],
    'GET' => [
         '/getApprovedBookings' => BookingController::class.'@getApprovedBookings',
         '/getRejectedBookings' => BookingController::class.'@getRejectedBookings'
    ],
    'DELETE' => [
         '/cancel/{id}' => BookingController::class.'@cancel'
    ]
];