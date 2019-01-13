<?php

namespace App\Repositories\Dao;

use \DateTime;
use Core\Datasource\DbDao;
use App\Repositories\StatusRepository;

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
            SELECT status.*, user.user_name
                FROM status
                    LEFT JOIN user ON status.user_id = user.id
                WHERE user.id = :user_id
                ORDER BY status.created_at DESC
        ";

        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }

    public function fetchAllByUserId($user_id): array {
        $sql = "
            SELECT status.*, user.user_name
                FROM status
                    LEFT JOIN user ON status.user_id = user.id
                WHERE user.id = :user_id
                ORDER BY status.created_at DESC
        ";

        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }

    public function fetchById($id) {
        $sql = "
            SELECT status.*, user.user_name
                FROM status
                    LEFT JOIN user ON status.user_id = user.id
                WHERE status.id = :id
                ORDER BY status.created_at DESC
        ";

        return $this->fetch($sql, array(':id' => $id));
    }
}
