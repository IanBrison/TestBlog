<?php

namespace App\Controllers;

use Core\Controller\Controller;
use App\Repositories\UserRepository;

class AccountController extends Controller {

    public function signup() {
        $values = array(
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signup')
        );
        return $this->render($values, 'account/signup');
    }

    public function register() {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signup', $token)) {
            return $this->redirect('/account/signup');
        }

        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
        } else if (!preg_match('/^\w{3,20}$/', $user_name)) {
            $errors[] = 'ユーザIDは半角英数字およびアンダースコアを3~20文字いないで入力してください';
        } else if (!$this->db_manager->get(UserRepository::class)->isUniqueUserName($user_name)) {
            $errors[] = 'ユーザIDは既に使用されています';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        } else if (4 > strlen($password) || strlen($password) > 30) {
            $errors[] = 'パスワードは4~30文字以内で入力してください';
        }

        if (count($errors) === 0) {
            $this->db_manager->get(UserRepository::class)->insert($user_name, $password);
            $this->session->setAuthenticated(true);

            $user = $this->db_manager->get(UserRepository::class)->fetchByUserName($user_name);
            $this->session->set('user', $user);

            return $this->redirect('/');
        }

        $error_values = array(
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signup')
        );
        return $this->render($error_values, 'account/signup');
    }
}
