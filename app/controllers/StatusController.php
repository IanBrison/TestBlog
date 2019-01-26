<?php

namespace App\Controllers;

use Core\Di\DiContainer as Di;
use Core\Request\Request;
use Core\Session\Session;
use Core\View\View;
use Core\Controller\Controller;
use Core\Exceptions\HttpNotFoundException;
use App\Repositories\AuthRepository;
use App\Repositories\StatusRepository;
use App\Repositories\UserRepository;
use App\Repositories\FollowRepository;
use App\Services\StatusListService;
use Presentation\Models\Components\ErrorList;

class StatusController extends Controller {

    public function index() {
        $session = Di::get(Session::class);
        $user = Di::get(AuthRepository::class)->user();
        $statusList = Di::get(StatusListService::class)->createPersonalStatusListViewModel($user);

        $error_list_view_model = $session->get('error_list_view_model', new ErrorList());
        $body = $session->get('body');
        $csrf_view_model = Di::get(View::class)->generateCsrfTokenViewModel();
        $values = array(
            'statusList' => $statusList,
            'error_list_view_model' => $error_list_view_model,
            'body' => $body,
            'csrf_view_model' => $csrf_view_model
        );
        return $this->render('status/index', $values);
    }

    public function post() {
        $request = Di::get(Request::class);

        $body = $request->getPost('body');

        $error_list_view_model = new ErrorList();
        if (!strlen($body)) {
            $error_list_view_model->addError('ひとことを入力してください');
        } else if (mb_strlen($body) > 200) {
            $error_list_view_model->addError('ひとことは200文字以内で入力してください');
        }

        $user = Di::get(AuthRepository::class)->user();
        if (!$error_list_view_model->hasErrors()) {
            $status = Di::get(StatusRepository::class)->insert($user, $body);
            return $this->redirect('/');
        }

        $session = Di::get(Session::class);
        $session->oneTimeSet('body', $body);
        $session->oneTimeSet('error_list_view_model', $error_list_view_model);
        return $this->redirect('/');
    }

    public function user($user_name) {
        $user = Di::get(UserRepository::class)->fetchByUserName($user_name);
        if ($user->isGuest()) {
            throw new HttpNotFoundException("No user found with username `$user_name`");
        }

        $statusList = Di::get(StatusListService::class)->createUsersStatusListViewModel($user);

        $is_following = null;
        $login_user = Di::get(AuthRepository::class)->user();
        if ($login_user->isSelf() && !$user->isSelf()) {
            $is_following = Di::get(FollowRepository::class)->isFollowing($login_user, $user);
        }

        $csrf_view_model = Di::get(View::class)->generateCsrfTokenViewModel();
        $values = array(
            'user' => $user,
            'statusList' => $statusList,
            'is_following' => $is_following,
            'csrf_view_model' => $csrf_view_model
        );
        return $this->render('status/user', $values);
    }

    public function show($status_id) {
        $status = Di::get(StatusRepository::class)->fetchById($status_id);
        $statusListItem = Di::get(StatusListService::class)->createStatusViewModel($status);

        $values = array(
            'statusListItem' => $statusListItem,
        );
        return $this->render('status/show', $values);
    }
}
