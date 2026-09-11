<?php
namespace App\Entities;

class User{
    public int $id;
    public string $name;
    public string $email;
    public string $phone;

    public function __construct( int $id, string $name, string $email, string $phone) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->email = $phone;
    } 
}