<?php

namespace App\Models\Entity\User;

use Core\Di\DiContainer as Di;
use Core\Datasource\GhostEntity;
use App\Models\User;
use App\Models\Entity\User\SelfUser;
use App\Models\Entity\User\OtherUser;
use App\Models\Entity\User\GuestUser;
use App\Repositories\AuthRepository;
use App\Repositories\StatusRepository;
use App\Repositories\FollowRepository;

class GhostUser extends GhostEntity implements User {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
        parent::__construct($id);
    }

    public function id(): int {
        return $this->id;
    }

    public function name(): string {
        return $this->realize()->name();
    }

    public function isSelf(): bool {
        return $this->realize()->isSelf();
    }

    public function isGuest(): bool {
        return $this->realize()->isGuest();
    }

    public function statuses(): array {
        return Di::get(StatusRepository::class)->fetchAllByUser($this);
    }

    public function personalStatuses(): array {
        return Di::get(StatusRepository::class)->fetchAllPersonalArchivesByUser($this);
    }

    public function followings(): array {
        return Di::get(FollowRepository::class)->getFollowings($this);
    }

    public function followers(): array {
        return Di::get(FollowRepository::class)->getFollowers($this);
    }

    public function realizeQuery(): string {
        $sql = "
            SELECT *
                FROM user
                WHERE id = {$this->id}
        ";
        return $sql;
    }

    public function realizeConstruction($row) {
        if ($row === false) {
            return new GuestUser();
        }
        $user = Di::get(AuthRepository::class)->user();
        if ($user->isSelf() && $user->id() === (int)$row['id']) {
            return new SelfUser($row['id'], $row['user_name']);
        }
        return new OtherUser($row['id'], $row['user_name']);
    }
}
