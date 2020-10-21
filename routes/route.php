<?php
/**
 * Created by PhpStorm.
 * User: dilshod
 * Date: 2020-06-06
 * Time: 12:14
 */

$router = new \Klein\Klein();

$router->post('/login', function (\Klein\Request $request, \Klein\Response $response) {
    return (new \Currency\Controller\UserController)->login($request, $response);
});

$router->post('/register', function () {
    return 'Registration page';
});

$router->get('/currencies', function (\Klein\Request $request, \Klein\Response $response) {
    (new \Currency\Service\AuthService)->isAuth($request, $response);
    return (new \Currency\Controller\CurrencyController)->currencies($request, $response);
});

$router->get('/currency/[i:id]', function (\Klein\Request $request, \Klein\Response $response) {
    (new \Currency\Service\AuthService)->isAuth($request, $response);
    return (new \Currency\Controller\CurrencyController)->currency($request, $response);
});

$router->dispatch();
