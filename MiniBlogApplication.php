<?php

class MiniBlogApplication extends Application {

    protected $login_action = array('account', 'signin');

    public function getRootDir() {
        return dirname(__FILE__);
    }

    protected function registerRoutes() {
        return array();
    }
}
