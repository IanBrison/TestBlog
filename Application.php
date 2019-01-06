<?php

use Core\BravelApplication;
use Core\Routing\Router;

class Application extends BravelApplication {

    protected $login_url = '/account/signin';

    public function getRootDir() :string {
        return dirname(__FILE__);
    }

    protected function registerRoutes() :array {
        return array(
            Router::get('/account', 'AccountController', 'index'),
            Router::get('/account/signin', 'AccountController', 'signin'),
            Router::post('/account/signin', 'AccountController', 'signin'),
            Router::get('/account/signup', 'AccountController', 'signup'),
            Router::post('/account/register', 'AccountController', 'register'),
        );
    }

    /*
     * configure method runs right after the Application class is initialized
     * write whatever you want the Application to build or run before the whole process starts
     */
    protected function configure() {}
}
