<?php

use Core\BravelApplication;

class Application extends BravelApplication {

    protected $login_action = array('account', 'signin');

    public function getRootDir() :string {
        return dirname(__FILE__);
    }

    protected function registerRoutes() :array {
        return array(
            '/account' => array('controller' => 'AccountController', 'action' => 'index'),
            '/account/:action' => array('controller' => 'AccountController')
        );
    }

    protected function configure() {}
}
