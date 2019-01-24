<?php

namespace App\Repositories\Dao;

use Core\Di\DiContainer as Di;
use Core\Datasource\DbDao;
use App\Repositories\AuthRepository;
use App\Repositories\FollowRepository;
use App\Models\User;
use App\Models\Entity\User\SelfUser;
use App\Models\Entity\User\OtherUser;

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
        $sql = "SELECT user.*
                    FROM user
                    LEFT JOIN following ON following.following_id = user.id
                    WHERE following.user_id = :user_id
        ";

        $rows = $this->fetchAll($sql, array(
            ':user_id' => $user->id(),
        ));
        $login_user = Di::get(AuthRepository::class)->user();
        $followings = array();
        foreach ($rows as $row) {
            if ($login_user->isSelf() && $login_user->id() === (int)$row['id']) {
                $followings[] = new SelfUser($row['id'], $row['user_name']);
            } else {
                $followings[] = new OtherUser($row['id'], $row['user_name']);
            }
        }

        return $followings;
    }

    public function getFollowers(User $user): array {
        $sql = "SELECT user.*
                    FROM user
                    LEFT JOIN following ON following.user_id = user.id
                    WHERE following.following_id = :user_id
        ";

        $rows = $this->fetchAll($sql, array(
            ':user_id' => $user->id(),
        ));
        $user = Di::get(AuthRepository::class)->user();
        $followers = array();
        foreach ($rows as $row) {
            if ($user->isSelf() && $user->id() === (int)$row['id']) {
                $followers[] = new SelfUser($row['id'], $row['user_name']);
            } else {
                $followers[] = new OtherUser($row['id'], $row['user_name']);
            }
        }

        return $followers;
    }

    public function isFollowing(User $user, User $user_might_be_following): bool {
        $sql = "SELECT COUNT(user_id) as count
                    FROM following
                    WHERE user_id = :user_id
                        AND following_id = :following_id
        ";

        $row = $this->fetch($sql, array(
            ':user_id' => $user->id(),
            ':following_id' => $user_might_be_following->id(),
        ));

        if ($row['count'] !== '0') {
            return true;
        }

        return false;
    }
}
