<?php

namespace App\Models\Entity;

use Core\Di\DiContainer as Di;
use App\Models\User;
use App\Repositories\StatusRepository;

class OtherUser implements User {

    private $id;
    private $name;

    private $statuses;

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
        $this->statuses = $this->statuses ?? Di::get(StatusRepository::class)->fetchAllByUserId($this->id);
        return $this->statuses;
    }
}
