<?php

namespace App\Controllers;

use Core\Di\DiContainer as Di;
use Core\Request\Request;
use Core\Controller\Controller;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use App\Repositories\FollowRepository;

class AccountController extends Controller {

    public function index() {
        $user = Di::get(AuthRepository::class)->user();
        $followings = $user->followings();

        $values = array(
            'user' => $user,
            'followings' => $followings
        );
        return $this->render('account/index', $values);
    }

    public function getSignin() {
        $values = array(
            'user_name' => '',
            '_token'    => $this->generateCsrfToken('account/signin')
        );
        return $this->render('account/signin', $values);
    }

    public function attemptSignin() {
        $request = Di::get(Request::class);

        $token = $request->getPost('_token');
        if (!$this->checkCsrfToken('account/signin', $token)) {
            return $this->redirect('/account/signin');
        }

        $user_name = $request->getPost('user_name');
        $password = $request->getPost('password');

        $result = Di::get(AuthRepository::class)->attemptSignin($user_name, $password);
        if ($result) {
            return $this->redirect('/');
        }

        $errors = array('ユーザIDかパスワードが不正です');
        $values = array(
            'errors' => $errors,
            'user_name' => $user_name,
            '_token' => $this->generateCsrfToken('account/signin')
        );
        return $this->render('/account/signin', $values);
    }

    public function signup() {
        $values = array(
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signup')
        );
        return $this->render('account/signup', $values);
    }

    public function register() {
        $request = Di::get(Request::class);

        $token = $request->getPost('_token');
        if (!$this->checkCsrfToken('account/signup', $token)) {
            return $this->redirect('/account/signup');
        }

        $user_name = $request->getPost('user_name');
        $password = $request->getPost('password');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
        } else if (!preg_match('/^\w{3,20}$/', $user_name)) {
            $errors[] = 'ユーザIDは半角英数字およびアンダースコアを3~20文字いないで入力してください';
        } else if (!Di::get(UserRepository::class)->isUniqueUserName($user_name)) {
            $errors[] = 'ユーザIDは既に使用されています';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        } else if (4 > strlen($password) || strlen($password) > 30) {
            $errors[] = 'パスワードは4~30文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $user = Di::get(UserRepository::class)->insert($user_name, $password);

            Di::get(AuthRepository::class)->setUser($user);

            return $this->redirect('/');
        }

        $error_values = array(
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signup')
        );
        return $this->render('account/signup', $error_values);
    }

    public function signout() {
        Di::get(AuthRepository::class)->signout();

        return $this->redirect('/account/signin');
    }

    public function follow() {
        $request = Di::get(Request::class);

        $token = $request->getPost('_token');
        if (!$this->checkCsrfToken('account/follow', $token)) {
            return $this->redirect('/account/follow');
        }

        $user_to_be_followed = Di::get(UserRepository::class)->fetchById($request->getPost('following_user_id'));
        if ($user_to_be_followed->isGuest()) {
            return $this->redirect('/account');
        }
        if (!$user_to_be_followed->isSelf()) {
            $result = Di::get(FollowRepository::class)->follow(Di::get(AuthRepository::class)->user(), $user_to_be_followed);
        }

        return $this->redirect('/user/'.$user_to_be_followed->name());
    }
}
