<?php

namespace App\Repositories\Dao;

use Core\Di\DiContainer as Di;
use Core\Datasource\DbDao;
use App\Models\User;
use App\Repositories\FollowRepository;

class FollowDbDao extends DbDao implements FollowRepository {

    public function follow(User $user, User $user_to_be_followed): bool {
        $sql = "INSERT INTO following VALUES(:user_id, :following_id)";

        $this->execute($sql, array(
            ':user_id' => $user->id(),
            ':following_id' => $user_to_be_followed->id(),
        ));
        return true;
    }

    public function getFollowings(User $user): array {
        return array();
    }

    public function getFollowers(User $user): array {
        return array();
    }

    public function isFollowing(User $user, User $user_might_followed): bool {
        $sql = "SELECT COUNT(user_id) as count
                    FROM following
                    WHERE user_id = :user_id
                        AND following_id = :following_id
        ";

        $row = $this->fetch($sql, array(
            ':user_id' => $user->id(),
            ':following_id' => $user_might_followed->id(),
        ));

        if ($row['count'] !== '0') {
            return true;
        }

        return false;
    }
}
