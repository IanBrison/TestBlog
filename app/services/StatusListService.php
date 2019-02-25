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
        $statuses = $user->personalStatuses();
        $statusListItems = array_map(function($status) {
            return Di::get(StatusListItem::class, $status);
        }, $statuses);
        return Di::get(StatusList::class, $statusListItems);
    }

    public function createUsersStatusListViewModel(User $user): StatusList {
        $statuses = $user->statuses();
        $statusListItems = array_map(function($status) {
            return Di::get(StatusListItem::class, $status);
        }, $statuses);
        return Di::get(StatusList::class, $statusListItems);
    }

    public function createStatusViewModel(Status $status): StatusListItem {
        return Di::get(StatusListItem::class, $status);
    }
}
