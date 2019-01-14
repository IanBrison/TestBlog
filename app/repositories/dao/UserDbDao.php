<?php

namespace App\Repositories\Dao;

use DateTime;
use Core\Di\DiContainer as Di;
use Core\Datasource\DbDao;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\Entity\SelfUser;
use App\Models\Entity\OtherUser;
use App\Models\Entity\GuestUser;

class UserDbDao extends DbDao implements UserRepository {

    public function insert($user_name, $password): User {
        $password = $this->hashPassword($password);
        $now = new DateTime();
        $created_at = $now->format('Y-m-d H:i:s');

        $sql = "INSERT INTO user(user_name, password, created_at) VALUES(:user_name, :password, :created_at)";

        $this->execute($sql, array(
            ':user_name'  => $user_name,
            ':password'   => $password,
            ':created_at' => $created_at,
        ));

        $id = $this->getLastInsertId();
        return new SelfUser($id, $user_name, $created_at);
    }

    public function fetchByUserName($user_name) {
        $sql = "SELECT * FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, array(':user_name' => $user_name));

        return $this->constructUserFromRow($row);
    }

    public function fetchById($id): User {
        $sql = "SELECT * FROM user WHERE id = :id";

        $row = $this->fetch($sql, array(':id' => $id));

        return $this->constructUserFromRow($row);
    }

    public function isUniqueUserName($user_name): bool {
        $sql = "SELECT COUNT(id) as count FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, array(':user_name' => $user_name));
        if ($row['count'] === '0') {
            return true;
        }

        return false;
    }

    public function attemptSignin($user_name, $password): User {
        $password = $this->hashPassword($password);

        $sql = "SELECT * FROM user WHERE user_name = :user_name AND password = :password";
        $row = $this->fetch($sql, array(':user_name' => $user_name, ':password' => $password));
        if (empty($row['user_name'])) {
            return new GuestUser();
        }

        return new SelfUser($row['id'], $row['user_name']);
    }

    protected function hashPassword($password) {
        return sha1($password . 'SecretKey');
    }

    private function constructUserFromRow($row) {
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
