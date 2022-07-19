<?php

namespace App\Interfaces;

interface UserInterface {

    public function getUsers();

    public function getUser($userId);
}
