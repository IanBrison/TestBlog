<?php

namespace App\Repositories;

interface UserRepository {

    public function insert($user_name, $password);

    public function fetchByUserName($user_name);

    public function isUniqueUserName($user_name);
}
