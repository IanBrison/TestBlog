<?php

namespace Presentation\Models\Components;

use Core\View\ViewModel;

class StatusList extends ViewModel {

    protected $template = 'components/status_list';

    private $statusListItems;

    public function __construct(array $statusListItems) {
        $this->statusListItems = $statusListItems;
    }

    public function hasStatuses(): bool {
        return count($this->statusListItems) > 0;
    }

    public function statusListItems() {
        return $this->statusListItems;
    }
}
