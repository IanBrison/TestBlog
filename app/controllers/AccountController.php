<?php

namespace App\Controllers;

use Core\Controller\Controller;

class AccountController extends Controller {

    public function signupAction() {
        return $this->render(array(
            '_token' => $this->generateCsrfToken('account/signup')
        ));
    }
}
