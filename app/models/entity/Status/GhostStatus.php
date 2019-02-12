<?php

namespace App\Models\Entity\Status;

use Core\Di\DiContainer as Di;
use Core\Datasource\GhostEntity;
use App\Repositories\AuthRepository;
use App\Repositories\ImageRepository;
use App\Models\User;
use App\Models\Entity\User\GhostUser;
use App\Models\Status;
use App\Models\TimeStamp;
use App\Models\Entity\Status\PublicStatus;
use App\Models\Entity\Status\MyStatus;
use App\Models\ValueObject\NormalTimeStamp;

class GhostStatus extends GhostEntity implements Status {

    private $id;

    public function __construct(int $id) {
        $this->id = $id;
        parent::__construct($id);
    }

    public function id(): int {
        return $this->id;
    }

    public function body(): string {
        return $this->realize()->body();
    }

    public function user(): User {
        return $this->realize()->user();
    }

    public function createdAt(): TimeStamp {
        return $this->realize()->createdAt();
    }

    public function isPostedBySelf(): bool {
        return $this->realize()->isPostedBySelf();
    }

    public function images(): array {
        return Di::get(ImageRepository::class)->fetchAllByStatus($this);
    }

    public function realizeQuery(): string {
        $sql = "
            SELECT *
                FROM status
                WHERE id = {$this->id}
        ";
        return $sql;
    }

    public function realizeConstruction($row) {
        if (!$row) {
            throw new HttpNotFoundException("No status found with status_id `$id`");
        }

        $user = Di::get(AuthRepository::class)->user();
        if ($user->isSelf() && $user->id() === (int)$row['user_id']) {
            return new MyStatus($row['id'], $row['body'], new GhostUser($row['user_id']), NormalTimeStamp::constructFromString($row['created_at']));
        }
        return new PublicStatus($row['id'], $row['body'], new GhostUser($row['user_id']), NormalTimeStamp::constructFromString($row['created_at']));
    }
}
