<?php

use Core\BravelApplication;

class Application extends BravelApplication {

    protected $login_action = array('account', 'signin');

    public function getRootDir() {
        return dirname(__FILE__);
    }

    protected function registerRoutes() {
        return array(
            '/account' => array('controller' => 'account', 'action' => 'index'),
            '/account/:action' => array('controller' => 'account')
        );
    }
}
