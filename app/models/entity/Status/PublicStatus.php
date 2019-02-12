<?php

namespace App\Models\Entity\Status;

use Core\Di\DiContainer as Di;
use App\Models\Status;
use App\Models\User;
use App\Models\TimeStamp;
use App\Repositories\ImageRepository;

class PublicStatus implements Status {

    private $id;
    private $body;
    private $user;
    private $created_at;

    public function __construct(int $id, string $body, User $user, TimeStamp $created_at) {
        $this->id = $id;
        $this->body = $body;
        $this->user = $user;
        $this->created_at = $created_at;
    }

    public function id(): int {
        return $this->id;
    }

    public function body(): string {
        return $this->body;
    }

    public function user(): User {
        return $this->user;
    }

    public function createdAt(): TimeStamp {
        return $this->created_at;
    }

    public function isPostedBySelf(): bool {
        return true;
    }

    public function images(): array {
        return Di::get(ImageRepository::class)->fetchAllByStatus($this);
    }
}
