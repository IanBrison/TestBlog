<?php

namespace App\Repositories\Dao;

use DateTime;
use Core\Di\DiContainer as Di;
use Core\Datasource\DbDao;
use Core\Exceptions\HttpNotFoundException;
use App\Repositories\AuthRepository;
use App\Repositories\StatusRepository;
use App\Models\User;
use App\Models\Entity\User\GhostUser;
use App\Models\Status;
use App\Models\Entity\Status\GhostStatus;
use App\Models\Entity\Status\PublicStatus;
use App\Models\Entity\Status\MyStatus;
use App\Models\ValueObject\NormalTimeStamp;

class StatusDbDao extends DbDao implements StatusRepository {

    public function insert(User $user, $body): Status {
        $now = new DateTime();
        $created_at = $now->format('Y-m-d H:i:s');

        $sql = "
            INSERT INTO status(user_id, body, created_at)
                VALUES(:user_id, :body, :created_at)
        ";

        $this->execute($sql, array(
            ':user_id' => $user->id(),
            ':body' => $body,
            ':created_at' => $created_at,
        ));

        $id = $this->getLastInsertId();
        return new MyStatus($id, $body, $user, NormalTimeStamp::constructFromString($created_at));
    }

    public function fetchAllPersonalArchivesByUser(User $user): array {
        $sql = "
            SELECT status.*
                FROM status
                LEFT JOIN following ON following.following_id = status.user_id AND following.user_id = :user_id
                WHERE status.user_id = :user_id
                OR following.user_id = :user_id
                ORDER BY created_at DESC
        ";

        $rows = $this->fetchAll($sql, array(':user_id' => $user->id()));

        $statuses = array();
        foreach ($rows as $row) {
            if ($user->isSelf() && $user->id() === (int)$row['user_id']) {
                $statuses[] = new MyStatus($row['id'], $row['body'], $user, NormalTimeStamp::constructFromString($row['created_at']));
            } else {
                $statuses[] = new PublicStatus($row['id'], $row['body'], new GhostUser($row['user_id']), NormalTimeStamp::constructFromString($row['created_at']));
            }
        }
        return $statuses;
    }

    public function fetchAllByUser(User $user): array {
        $sql = "
            SELECT *
                FROM status
                WHERE user_id = :user_id
                ORDER BY created_at DESC
        ";

        $rows = $this->fetchAll($sql, array(':user_id' => $user->id()));

        $statuses = array();
        foreach ($rows as $row) {
            if ($user->isSelf()) {
                $statuses[] = new MyStatus($row['id'], $row['body'], $user, NormalTimeStamp::constructFromString($row['created_at']));
            } else {
                $statuses[] = new PublicStatus($row['id'], $row['body'], new GhostUser($row['user_id']), NormalTimeStamp::constructFromString($row['created_at']));
            }
        }
        return $statuses;
    }

    public function fetchById($id): Status {
        return new GhostStatus($id);
    }
}
