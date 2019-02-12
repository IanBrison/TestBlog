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
use App\Services\Usecase\CreateStatus;
use Presentation\Models\Components\ErrorList;

class StatusController extends Controller {

    public function index() {
        $user = Di::get(AuthRepository::class)->user();
        $statusList = Di::get(StatusListService::class)->createPersonalStatusListViewModel($user);
        $createStatusService = new CreateStatus();
        $statusPostForm = $createStatusService->generatePostFormViewModel();

        $error_list_view_model = Di::get(Session::class)->get('error_list_view_model', new ErrorList());
        $csrf_view_model = Di::get(View::class)->generateCsrfTokenViewModel();
        $values = array(
            'statusList' => $statusList,
            'statusPostForm' => $statusPostForm,
            'error_list_view_model' => $error_list_view_model,
            'csrf_view_model' => $csrf_view_model
        );
        return $this->render('status/index', $values);
    }

    public function post() {
        $createStatusService = new CreateStatus();
        if ($createStatusService->createFromPost()) {
            return $this->redirect('/');
        }

        $error_list_view_model = new ErrorList();
        foreach ($createStatusService->retrieveErrors() as $error) {
            $error_list_view_model->addError($error);
        }
        Di::get(Session::class)->oneTimeSet('error_list_view_model', $error_list_view_model);
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
