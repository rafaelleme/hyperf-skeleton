<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\HttpServer\Router\Router;

$userRoutes = function () {
    Router::addRoute(['GET'], '', 'App\Controller\Users\UsersController@list');
    Router::addRoute(['POST'], '', 'App\Controller\Users\UsersController@create');
    Router::addRoute(['DELETE'], '/{userId}', 'App\Controller\Users\UsersController@delete');
};

$transactionRoutes = function () {
    Router::addRoute(['POST'], '', 'App\Controller\Users\TransactionsController@create');
};

/*
 * Routes register
 */
Router::addRoute(['GET'], '/ping', 'App\Controller\IndexController@ping');

/*
 * Api routes
 */
Router::addGroup('/api', function () use ($userRoutes, $transactionRoutes) {
    Router::addGroup('/users', $userRoutes);
    Router::addGroup('/transactions', $transactionRoutes);
});
