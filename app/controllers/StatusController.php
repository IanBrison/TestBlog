<?php

namespace App\Controllers;

use Core\Di\DiContainer as Di;
use Core\Session\Session;
use Core\Request\Request;
use Core\Controller\Controller;
use App\Repositories\StatusRepository;

class StatusController extends Controller {

    public function index() {
        $user = Di::get(Session::class)->get('user');
        $statuses = Di::get(StatusRepository::class)->fetchAllPersonalArchivesByUserId($user['id']);

        $values = array(
            'statuses' => $statuses,
            'body' => '',
            '_token' => $this->generateCsrfToken('status/post')
        );
        return $this->render('status/index', $values);
    }

    public function post() {
        $request = Di::get(Request::class);
        $token = $request->getPost('_token');
        if (!$this->checkCsrfToken('status/post', $token)) {
            return $this->redirect('/');
        }

        $body = $request->getPost('body');

        $errors = array();

        if (!strlen($body)) {
            $errors[] = 'ひとことを入力してください';
        } else if (mb_strlen($body) > 200) {
            $errors[] = 'ひとことは200文字以内で入力してください';
        }

        $user = Di::get(Session::class)->get('user');
        if (count($errors) === 0) {
            Di::get(StatusRepository::class)->insert($user['id'], $body);
            return $this->redirect('/');
        }

        $statuses = Di::get(StatusRepository::class)->fetchAllPersonalArchivesByUserId($user['id']);

        $values = array(
            'errors' => $errors,
            'statuses' => $statuses,
            'body' => $body,
            '_token' => $this->generateCsrfToken('status/post')
        );
        return $this->render('status/index', $values);
    }
}
