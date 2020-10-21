<?php
/**
 * Created by PhpStorm.
 * User: dilshod
 * Date: 2020-06-06
 * Time: 13:54
 */
namespace Currency\Controller;

use Currency\Service\AuthService;
use Klein\Request;
use Klein\Response;

class UserController
{
    public function login(Request $request, Response $response)
    {
        $email = trim($request->paramsPost()->get('email'));
        $password = $request->paramsPost()->get('password');

        $token = (new AuthService)->login($email, $password);

        if ($token) {
            return $response->json([
                'data' => [
                    'token' => $token,
                    'success' => true
                ],
            ]);
        }

        return $response->json([
            'data' => [
                'success' => false
            ]
        ]);
    }
}
