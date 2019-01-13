<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepository {

    public function insert($user_name, $password);

    public function fetchByUserName($user_name);

    public function fetchById($id): User;

    public function isUniqueUserName($user_name): bool;

    public function attemptSignin($user_name, $password);
}
