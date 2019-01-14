<?php

namespace App\Repositories;

use App\Models\User;

interface AuthRepository {

    public function user(): User;

    public function isAuthenticated(): bool;

    public function attemptSignin(string $user_name, string $password): bool;

    public function setUser(User $user);
}
