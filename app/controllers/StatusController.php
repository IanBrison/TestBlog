<?php

namespace App\Controllers;

use Core\Di\DiContainer as Di;
use Core\Request\Request;
use Core\Controller\Controller;
use Core\Exceptions\HttpNotFoundException;
use App\Repositories\AuthRepository;
use App\Repositories\StatusRepository;
use App\Repositories\UserRepository;
use App\Repositories\FollowRepository;
use Presentation\Models\Components\CsrfToken;
use Presentation\Models\Components\ErrorList;

class StatusController extends Controller {

    public function index() {
        $user = Di::get(AuthRepository::class)->user();
        $statuses = Di::get(StatusRepository::class)->fetchAllPersonalArchivesByUserId($user->id());

        $csrf_view_model = new CsrfToken($this->generateCsrfToken('status/post'));
        $values = array(
            'statuses' => $statuses,
            'body' => '',
            'csrf_view_model' => $csrf_view_model
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

        $error_list_view_model = new ErrorList();
        if (!strlen($body)) {
            $error_list_view_model->addError('ひとことを入力してください');
        } else if (mb_strlen($body) > 200) {
            $error_list_view_model->addError('ひとことは200文字以内で入力してください');
        }

        $user = Di::get(AuthRepository::class)->user();
        if (!$error_list_view_model->hasErrors()) {
            $status = Di::get(StatusRepository::class)->insert($user->id(), $body);
            return $this->redirect('/');
        }

        $statuses = Di::get(StatusRepository::class)->fetchAllPersonalArchivesByUserId($user->id());

        $csrf_view_model = new CsrfToken($this->generateCsrfToken('status/post'));
        $values = array(
            'error_list_view_model' => $error_list_view_model,
            'statuses' => $statuses,
            'body' => $body,
            'csrf_view_model' => $csrf_view_model
        );
        return $this->render('status/index', $values);
    }

    public function user($params) {
        $user_name = $params['user_name'];
        $user = Di::get(UserRepository::class)->fetchByUserName($user_name);
        if ($user->isGuest()) {
            throw new HttpNotFoundException("No user found with username `$user_name`");
        }

        $statuses = Di::get(StatusRepository::class)->fetchAllByUserId($user->id());

        $is_following = null;
        $login_user = Di::get(AuthRepository::class)->user();
        if ($login_user->isSelf() && !$user->isSelf()) {
            $is_following = Di::get(FollowRepository::class)->isFollowing($login_user, $user);
        }

        $csrf_view_model = new CsrfToken($this->generateCsrfToken('account/follow'));
        $values = array(
            'user' => $user,
            'statuses' => $statuses,
            'is_following' => $is_following,
            'csrf_view_model' => $csrf_view_model
        );
        return $this->render('status/user', $values);
    }

    public function show($params) {
        $status_id = $params['id'];
        $status = Di::get(StatusRepository::class)->fetchById($status_id);
        if (!$status) {
            throw new HttpNotFoundException("No status found with status_id `$status_id`");
        }

        $values = array(
            'status' => $status,
        );
        return $this->render('status/show', $values);
    }
}
