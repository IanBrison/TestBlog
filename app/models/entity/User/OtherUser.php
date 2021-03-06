<?php

namespace App\Models\Entity\User;

use Core\Di\DiContainer as Di;
use App\Models\User;
use App\Repositories\StatusRepository;
use App\Repositories\FollowRepository;
use App\Repositories\ImageRepository;

class OtherUser implements User {

    private $id;
    private $name;

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
        return false;
    }

    public function isGuest(): bool {
        return false;
    }

    public function statuses(): array {
        return Di::get(StatusRepository::class)->fetchAllByUser($this);
    }

    public function personalStatuses(): array {
        throw new \Exception('No personal statuses for other user');
    }

    public function followings(): array {
        return Di::get(FollowRepository::class)->getFollowings($this);
    }

    public function followers(): array {
        return Di::get(FollowRepository::class)->getFollowers($this);
    }

    public function images(): array {
        return Di::get(ImageRepository::class)->fetchAllByUser($this);
    }
}
