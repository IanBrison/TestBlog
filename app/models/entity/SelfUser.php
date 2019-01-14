<?php

namespace App\Models\Entity;

use Core\Di\DiContainer as Di;
use App\Models\User;
use App\Repositories\StatusRepository;
use App\Repositories\FollowRepository;

class SelfUser implements User {

    private $id;
    private $name;

    private $statuses;
    private $followings;
    private $followers;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): int {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function isSelf(): bool {
        return true;
    }

    public function isGuest(): bool {
        return false;
    }

    public function statuses(): array {
        $this->statuses = $this->statuses ?? Di::get(StatusRepository::class)->fetchAllByUserId($this->id);
        return $this->statuses;
    }

    public function followings(): array {
        $this->followings = $this->followings ?? Di::get(FollowRepository::class)->getFollowings($this);
        return $this->followings;
    }

    public function followers(): array {
        $this->followers = $this->followers ?? Di::get(FollowRepository::class)->getFollowers($this);
        return $this->followers;
    }
}
