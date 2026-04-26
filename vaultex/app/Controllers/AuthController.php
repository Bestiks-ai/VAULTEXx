<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\Lang;
use App\Models\User;
use App\Middleware\CsrfMiddleware;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if (Session::has('user_id')) {
            header('Location: /dashboard');
            exit;
        }

        $twig = $this->getTwig();
        echo $twig->render('login.twig', [
            'csrf_token' => CsrfMiddleware::getToken(),
            'lang' => Lang::getCurrent(),
        ]);
    }

    public function login(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Invalid email or password');
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            Session::setFlash('error', 'Invalid email or password');
            header('Location: /login');
            exit;
        }

        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::regenerate();

        $this->userModel->updateLastLogin($user['id']);

        header('Location: /dashboard');
        exit;
    }

    public function showRegister(): void
    {
        if (Session::has('user_id')) {
            header('Location: /dashboard');
            exit;
        }

        $twig = $this->getTwig();
        echo $twig->render('register.twig', [
            'csrf_token' => CsrfMiddleware::getToken(),
            'lang' => Lang::getCurrent(),
        ]);
    }

    public function register(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'All fields are required');
            header('Location: /register');
            exit;
        }

        if ($password !== $passwordConfirm) {
            Session::setFlash('error', 'Passwords do not match');
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 8) {
            Session::setFlash('error', 'Password must be at least 8 characters');
            header('Location: /register');
            exit;
        }

        if ($this->userModel->findByEmail($email)) {
            Session::setFlash('error', 'Email already registered');
            header('Location: /register');
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->userModel->create($email, $passwordHash);

        Session::set('user_id', $userId);
        Session::set('user_email', $email);

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        Session::destroy();
        header('Location: /');
        exit;
    }

    private function getTwig(): \Twig\Environment
    {
        $loader = new \Twig\Loader\FilesystemLoader(ROOT . '/views');
        return new \Twig\Environment($loader, [
            'cache' => ROOT . '/var/cache/twig',
            'debug' => ($_ENV['APP_DEBUG'] ?? false),
        ]);
    }
}
