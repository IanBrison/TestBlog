<?php

namespace App\Repositories\Dao;

use \DateTime;
use Core\Di\DiContainer as Di;
use Core\Datasource\DbDao;
use Core\Session\Session;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\Entity\SelfUser;
use App\Models\Entity\OtherUser;

class UserDbDao extends DbDao implements UserRepository {

    public function insert($user_name, $password) {
        $password = $this->hashPassword($password);
        $now = new DateTime();

        $sql = "INSERT INTO user(user_name, password, created_at) VALUES(:user_name, :password, :created_at)";

        $stmt = $this->execute($sql, array(
            ':user_name'  => $user_name,
            ':password'   => $password,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    public function fetchByUserName($user_name) {
        $sql = "SELECT * FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, array(':user_name' => $user_name));
    }

    public function fetchById($id): User {
        $sql = "SELECT * FROM user WHERE id = :id";

        $row = $this->fetch($sql, array(':id' => $id));

        $login_user = Di::get(Session::class)->get('user');
        if ($login_user['id'] == $row['id']) {
            return new SelfUser($row['id'], $row['user_name']);
        }
        return new OtherUser($row['id'], $row['user_name']);
    }

    public function isUniqueUserName($user_name): bool {
        $sql = "SELECT COUNT(id) as count FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, array(':user_name' => $user_name));
        if ($row['count'] === '0') {
            return true;
        }

        return false;
    }

    public function attemptSignin($user_name, $password) {
        $password = $this->hashPassword($password);

        $sql = "SELECT * from user WHERE user_name = :user_name AND password = :password";
        $row = $this->fetch($sql, array(':user_name' => $user_name, ':password' => $password));
        if (empty($row['user_name'])) {
            return false;
        }

        return $row;
    }

    protected function hashPassword($password) {
        return sha1($password . 'SecretKey');
    }
}
