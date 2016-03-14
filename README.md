# slim-acl
ACL middleware for slim, using Zend\Permissions\Acl

[![Coverage Status](https://coveralls.io/repos/slimphp-api/slim-acl/badge.svg?branch=master&service=github)](https://coveralls.io/github/slimphp-api/slim-acl?branch=master)
[![Code Climate](https://codeclimate.com/github/slimphp-api/slim-acl/badges/gpa.svg)](https://codeclimate.com/github/slimphp-api/slim-acl)
[![Build Status](https://travis-ci.org/slimphp-api/slim-acl.svg?branch=master)](https://travis-ci.org/slimphp-api/slim-acl)

Configuration passed into the middleware constructor should look like:

```
[
    'default_role' => 'guest',
    'roles' => [
        'guest' => [],
        'user'  => ['guest'],
        'admin' => ['user']
    ],
    /*
     * just a list of generic resources for manual checking
     * specified here so can be used in the code if needs be
     */
     'resources' => [
         'banana' => null,
         'orange' => null,
     ],
    // where we specify the guarding!
    'guards' => [
        /*
         * list of resource to roles to permissions
         * optional
         * if included all resources default to deny unless specified.
         */
        'resources' => [
            ['banana', ['user'], ['peel']],
            ['banana', ['admin']],
            ['orange', ['guest'], ['peel']],
            ['orange', ['user'], ['eat']],
        ],
        /*
         * list of literal routes for guarding.
         * optional
         * if included all routes default to deny unless specified.
         * Similar format to resource 'resource' route, roles, 'permission' action
         * ['route', ['roles'], ['methods',' methods1']]
         */
        'routes' => [
            ['/home',       ['user'],  ['get']],
            ['/user',       ['admin'], ['get', 'post']], // $app->map(['POST', 'PUT'], '/user', ...);
            ['/user/{id}',  ['user'],  ['get']], // $app->map(['GET'], '/user/{id}', ...);
        ],
        /*
         * list of callables to resolve against
         * optional
         * if included all callables default to deny unless specified.
         * 'permission' section is combined into the callable section
         * ['callable', ['roles']]
         */
        'callables' => [
            ['App\Controller\UserController',           ['user']], // $app->map(['GET'], '/user',      'App\Controller\UserController'); class with __invoke
            ['App\Controller\UserController:getAction', ['user']], // $app->map(['GET'], '/user/{id}', 'App\Controller\UserController:getAction'); class and method
        ]
    ]
]
```

**Important:** The route should be determined before the app middlewares are being fired by:

```
$app = new Slim\App([
    'settings'  => [
        'determineRouteBeforeAppMiddleware' => true,
    ]
]);
```
