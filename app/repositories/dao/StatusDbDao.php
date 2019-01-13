<?php

namespace App\Repositories\Dao;

use \DateTime;
use Core\Di\DiContainer as Di;
use Core\Datasource\DbDao;
use Core\Session\Session;
use App\Repositories\StatusRepository;
use App\Models\Status;
use App\Models\Entity\PublicStatus;
use App\Models\Entity\MyStatus;

class StatusDbDao extends DbDao implements StatusRepository {

    public function insert($user_id, $body): bool {
        $now = new DateTime();

        $sql = "
            INSERT INTO status(user_id, body, created_at)
                VALUES(:user_id, :body, :created_at)
        ";

        $stmt = $this->execute($sql, array(
            ':user_id' => $user_id,
            ':body' => $body,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ));
        return true;
    }

    public function fetchAllPersonalArchivesByUserId($user_id): array {
        $sql = "
            SELECT *
                FROM status
                WHERE user_id = :user_id
                ORDER BY created_at DESC
        ";

        $rows = $this->fetchAll($sql, array(':user_id' => $user_id));

        $login_user = Di::get(Session::class)->get('user');
        $statuses = array();
        foreach ($rows as $row) {
            if ($row['user_id'] == $login_user['id']) {
                $statuses[] = new MyStatus($row['id'], $row['body'], $row['user_id'], $row['created_at']);
            } else {
                $statuses[] = new PublicStatus($row['id'], $row['body'], $row['user_id'], $row['created_at']);
            }
        }
        return $statuses;
    }

    public function fetchAllByUserId($user_id): array {
        $sql = "
            SELECT *
                FROM status
                WHERE user_id = :user_id
                ORDER BY created_at DESC
        ";

        $rows = $this->fetchAll($sql, array(':user_id' => $user_id));

        $login_user = Di::get(Session::class)->get('user');
        $statuses = array();
        foreach ($rows as $row) {
            if ($row['user_id'] == $login_user['id']) {
                $statuses[] = new MyStatus($row['id'], $row['body'], $row['user_id'], $row['created_at']);
            } else {
                $statuses[] = new PublicStatus($row['id'], $row['body'], $row['user_id'], $row['created_at']);
            }
        }
        return $statuses;
    }

    public function fetchById($id): Status {
        $sql = "
            SELECT *
                FROM status
                WHERE id = :id
                ORDER BY created_at DESC
        ";

        $row = $this->fetch($sql, array(':id' => $id));

        $login_user = Di::get(Session::class)->get('user');
        if ($row['user_id'] == $login_user['id']) {
            return new MyStatus($row['id'], $row['body'], $row['user_id'], $row['created_at']);
        }
        return new PublicStatus($row['id'], $row['body'], $row['user_id'], $row['created_at']);
    }
}
