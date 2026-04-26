<?php

namespace App\Middleware;

use App\Core\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        Session::init();

        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }
    }
}
