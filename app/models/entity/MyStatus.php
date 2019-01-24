<?php

namespace App\Models\Entity;

use Core\Di\DiContainer as Di;
use App\Models\Status;
use App\Models\User;
use App\Models\TimeStamp;
use App\Repositories\UserRepository;

class MyStatus implements Status {

    private $id;
    private $body;
    private $user_id;
    private $created_at;

    public function __construct(int $id, string $body, string $user_id, TimeStamp $created_at) {
        $this->id = $id;
        $this->body = $body;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
    }

    public function id(): int {
        return $this->id;
    }

    public function body(): string {
        return $this->body;
    }

    public function user(): User {
        return Di::get(UserRepository::class)->fetchById($this->user_id);
    }

    public function createdAt(): TimeStamp {
        return $this->created_at;
    }

    public function isPostedBySelf(): bool {
        return true;
    }
}
