<?php

namespace Presentation\Models\Components;

use Core\View\ViewModel;
use App\Models\Status;

class StatusListItem extends ViewModel {

    protected $template = 'components/status_list_item';

    private $status;

    public function __construct(Status $status) {
        $this->status = $status;
    }

    public function getUserName(): string {
        return $this->status->user()->name();
    }

    public function getBody(): string {
        return $this->status->body();
    }

    public function getCreatedAt(): string {
        return $this->status->createdAt()->showDate();
    }

    public function getUserUrl(): string {
        return '/user/' . $this->status->user()->name();
    }

    public function getStatusUrl(): string {
        return '/status/' . $this->status->id();
    }
}
