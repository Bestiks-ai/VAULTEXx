<?php

namespace App\Middleware;

use App\Core\Session;

class CsrfMiddleware
{
    public function handle(): void
    {
        Session::init();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            $storedToken = Session::get('csrf_token');

            if (empty($token) || empty($storedToken) || !hash_equals($storedToken, $token)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }

    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public static function getToken(): string
    {
        Session::init();
        $token = Session::get('csrf_token');
        if (empty($token)) {
            $token = self::generateToken();
        }
        return $token;
    }
}
