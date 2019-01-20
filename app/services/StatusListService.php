<?php

namespace App\Services;

use Core\Di\DiContainer as Di;
use App\Models\User;
use App\Models\Status;
use App\Repositories\StatusRepository;
use Presentation\Models\Components\StatusList;
use Presentation\Models\Components\StatusListItem;

class StatusListService {

    public function createPersonalStatusListViewModel(User $user): StatusList {
        $statuses = Di::get(StatusRepository::class)->fetchAllPersonalArchivesByUserId($user->id());
        $statusListItems = array_map(function($status) {
            return new StatusListItem($status);
        }, $statuses);
        return new StatusList($statusListItems);
    }

    public function createUsersStatusListViewModel(User $user): StatusList {
        $statuses = Di::get(StatusRepository::class)->fetchAllByUserId($user->id());
        $statusListItems = array_map(function($status) {
            return new StatusListItem($status);
        }, $statuses);
        return new StatusList($statusListItems);
    }

    public function createStatusViewModel(Status $status): StatusListItem {
        return new StatusListItem($status);
    }
}
