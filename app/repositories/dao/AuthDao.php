<?php

namespace App\Repositories\Dao;

use Core\Di\DiContainer as Di;
use Core\Session\Session;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\Entity\GuestUser;

class AuthDao implements AuthRepository {

    private $user;
    private $is_authenticated;

    public function __construct() {
        $this->user = Di::get(Session::class)->get('user', null) ?? new GuestUser();
        $this->is_authenticated = Di::get(Session::class)->isAuthenticated();
    }

    public function user(): User {
        return $this->user;
    }

    public function isAuthenticated(): bool {
        return $this->is_authenticated;
    }

    public function attemptSignin(string $user_name, string $password): bool {
        $user = Di::get(UserRepository::class)->attemptSignin($user_name, $password);
        if (!$user->isSelf()) {
            return false;
        }
        $this->setUser($user);
        return true;
    }

    public function setUser(User $user) {
        if (!$user->isSelf()) {
            throw \Exception('Unexpected user tried to be set');
        }

        Di::get(Session::class)->set('user', $user)->setAuthenticated(true);
        $this->user = $user;
    }
}
