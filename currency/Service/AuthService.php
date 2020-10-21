<?php
/**
 * Created by PhpStorm.
 * User: dilshod
 * Date: 2020-06-07
 * Time: 17:45
 */

namespace Currency\Service;


use Currency\Model\User;
use Exception;
use Firebase\JWT\JWT;
use Klein\Request;
use Klein\Response;
use Noodlehaus\Config;

class AuthService
{
    protected $config;

    public function __construct()
    {
        $this->config = Config::load(APP_DIR . '/config/currency.php');
    }

    public function login($email, $password)
    {
        $user = new User();
        $user_row = $user->getByColumn('email', $email);

        if (empty($user_row)) {
            return false;
        }

        if (password_verify($password, $user_row['password'])) {
            return $this->getToken($user_row);
        }
    }

    public function getToken($user)
    {

        $secret_key = $this->config->get('jwt_secret');

        $token_id    = base64_encode(md5(uniqid($user['email'], true)));
        $issued_at   = time();
        $not_before  = $issued_at + 10;
        $expire     = $not_before + (60 * 60 * 24);
        $app_name = $this->config->get('app_name');

        $data = [
            'iat'  => $issued_at,
            'jti'  => $token_id,
            'iss'  => $app_name,
            'nbf'  => $not_before,
            'exp'  => $expire,
            'data' => [
                'user_id' => $user['id']
            ]
        ];

        $token = JWT::encode(
            $data,
            $secret_key,
            'HS512'
        );

        return $token;
    }

    public function isAuth(Request $request, Response $response)
    {
        $tokenHeader = $request->headers()->get('authorization');

        if ($tokenHeader) {
            list($jwt) = sscanf( $tokenHeader, 'Bearer %s');

            if ($jwt) {
                try {
                    $secret_key = $this->config->get('jwt_secret');
                    JWT::decode($jwt, $secret_key, ['HS512']);
                } catch (Exception $e) {
                    $response->code(401)->send();
                }
            } else {
                $response->code(401)->send();
            }
        } else {
            $response->code(401)->send();
        }
    }
}
