<?php
/*
 * config file for di container
 * singletons are instances that are to be saved when they are constructed
 * aliases are for choosing the right classes to use for construction
 */
return [
    'singletons' => [
        Core\Request\Request::class,
        Core\Response\Response::class,
        Core\Session\Session::class,
        Core\Datasource\DbManager::class,
        Core\Routing\Router::class,
    ],

    'aliases' => [
        App\Repositories\AuthRepository::class => App\Repositories\Dao\AuthDao::class,

        App\Repositories\UserRepository::class => App\Repositories\Dao\UserDbDao::class,
        App\Repositories\StatusRepository::class => App\Repositories\Dao\StatusDbDao::class,
        App\Repositories\FollowRepository::class => App\Repositories\Dao\FollowDbDao::class,
    ]
];
