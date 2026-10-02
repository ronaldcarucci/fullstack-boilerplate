<?php

use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {

    $routes->scope('/', function (RouteBuilder $builder) {
        $builder->connect('/', ['controller' => 'Spa', 'action' => 'index']);
        $builder->connect('/*', ['controller' => 'Spa', 'action' => 'index']);
    });

    $routes->scope('/api/v1', function (RouteBuilder $builder): void {

        $builder->connect('/auth/register', ['controller' => 'Api', 'action' => 'register'], ['_method' => 'POST']);
        $builder->connect('/auth/login', ['controller' => 'Api', 'action' => 'login'], ['_method' => 'POST']);
        $builder->connect('/auth/refresh', ['controller' => 'Api', 'action' => 'refresh'], ['_method' => 'POST']);
        $builder->connect('/auth/logout', ['controller' => 'Api', 'action' => 'logout'], ['_method' => 'POST']);

        $builder->connect(
            '/{parentResource}/{parentId}/{resource}/{id}',
            ['controller' => 'CrudResource', 'action' => 'linkResources'],
            ['parentResource' => '[a-zA-Z_-]+', 'parentId' => '\d+', 'resource' => '[a-zA-Z_-]+', 'id' => '\d+', '_method' => 'POST']
        );

        $builder->connect(
            '/{parentResource}/{parentId}/{resource}/{id}',
            ['controller' => 'CrudResource', 'action' => 'unlinkResources'],
            ['parentResource' => '[a-zA-Z_-]+', 'parentId' => '\d+', 'resource' => '[a-zA-Z_-]+', 'id' => '\d+', '_method' => 'DELETE']
        );

        $builder->connect(
            '/{parentResource}/{parentId}/{resource}',
            ['controller' => 'CrudResource', 'action' => 'indexNested'],
            ['parentResource' => '[a-zA-Z_-]+', 'parentId' => '\d+', 'resource' => '[a-zA-Z_-]+', '_method' => 'GET']
        );

        $builder->connect(
            '/{parentResource}/{parentId}/{resource}',
            ['controller' => 'CrudResource', 'action' => 'addNested'],
            ['parentResource' => '[a-zA-Z_-]+', 'parentId' => '\d+', 'resource' => '[a-zA-Z_-]+', '_method' => 'POST']
        );

        $builder->connect('/{resource}', ['controller' => 'CrudResource', 'action' => 'index'], ['_method' => 'GET', 'resource' => '[a-zA-Z_-]+']);
        $builder->connect('/{resource}', ['controller' => 'CrudResource', 'action' => 'add'], ['_method' => 'POST', 'resource' => '[a-zA-Z_-]+']);
        $builder->connect('/{resource}/{id}', ['controller' => 'CrudResource', 'action' => 'view'], ['_method' => 'GET', 'id' => '\d+', 'resource' => '[a-zA-Z_-]+']);
        $builder->connect('/{resource}/{id}', ['controller' => 'CrudResource', 'action' => 'edit'], ['_method' => ['PUT', 'PATCH'], 'id' => '\d+', 'resource' => '[a-zA-Z_-]+']);
        $builder->connect('/{resource}/{id}', ['controller' => 'CrudResource', 'action' => 'delete'], ['_method' => 'DELETE', 'id' => '\d+', 'resource' => '[a-zA-Z_-]+']);
    });
};
