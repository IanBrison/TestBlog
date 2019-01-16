<?php

namespace Presentation\Models\Components;

use Core\View\ViewModel;

class ErrorList extends ViewModel {

    protected $template = 'components/error_list';

    private $errors;

    public function __construct() {
        $this->errors = array();
    }

    public function addError(string $statement): ErrorList {
        $this->errors[] = $statement;
        return $this;
    }

    public function hasErrors(): bool {
        return count($this->errors) > 0;
    }

    public function errors(): array {
        return $this->errors;
    }
}
