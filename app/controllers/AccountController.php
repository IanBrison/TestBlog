<?php

namespace App\Controllers;

use Core\Di\DiContainer as Di;
use Core\Session\Session;
use Core\Request\Request;
use Core\Controller\Controller;
use App\Repositories\UserRepository;

class AccountController extends Controller {

    protected $auth_actions = array('index');

    public function index() {
        return 'hello';
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

        $user_name = $request->getPost('user_name');
        $password = $request->getPost('password');

        $user = Di::get(UserRepository::class)->attemptSignin($user_name, $password);
        if ($user === false) {
            return $this->redirect('/account/signin');
        }

        $session = Di::get(Session::class);
        $session->set('user', $user);
        $session->setAuthenticated(true);
        return $this->redirect('/account');
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
        if (!$request->isPost()) {
            $this->forward404();
        }

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
            $session = Di::get(Session::class);
            Di::get(UserRepository::class)->insert($user_name, $password);
            $session->setAuthenticated(true);

            $user = Di::get(UserRepository::class)->fetchByUserName($user_name);
            $session->set('user', $user);

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
}
