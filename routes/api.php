<?php
use App\controllers\AuthController;
use App\controllers\BookingController;

return [
    'POST' => [
         '/register' => AuthController::class.'@register',
         '/login' => AuthController::class.'@login',
         '/book' => BookingController::class.'@book',
         '/cancel/{id}' => BookingController::class.'@cancel'

    ]
];