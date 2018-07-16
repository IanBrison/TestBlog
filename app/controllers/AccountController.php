<?php

namespace App\Controllers;

use Core\Controller\Controller;

class AccountController extends Controller {

    public function signupAction() {
        $values = array(
            '_token' => $this->generateCsrfToken('account/signup')
        );
        return $this->render($values, 'account/signup');
    }
}
