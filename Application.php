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
            Router::get('/', 'StatusController', 'index')->withAuth(),
            Router::get('/status/:id', 'StatusController', 'show'),
            Router::post('/status/post', 'StatusController', 'post'),

            Router::get('/user/:user_name', 'StatusController', 'user'),

            Router::get('/account', 'AccountController', 'index')->withAuth(),
            Router::get('/account/signin', 'AccountController', 'getSignin'),
            Router::post('/account/signin', 'AccountController', 'attemptSignin'),
            Router::get('/account/signup', 'AccountController', 'signup'),
            Router::post('/account/register', 'AccountController', 'register'),
            Router::get('/account/signout', 'AccountController', 'signout'),
            Router::post('/account/follow', 'AccountController', 'follow'),
        );
    }

    /*
     * configure method runs right after the Application class is initialized
     * write whatever you want the Application to build or run before the whole process starts
     */
    protected function configure() {}
}
