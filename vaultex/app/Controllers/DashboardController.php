<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\Lang;
use App\Models\Wallet;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DashboardController
{
    public function index(): void
    {
        // Check auth
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }

        $userId = Session::get('user_id');
        $walletModel = new Wallet();
        
        $wallets = [];
        $totalBalance = 0;

        // Get user wallets (placeholder - should query DB)
        // For demo purposes, showing empty state
        
        $twig = $this->getTwig();
        echo $twig->render('dashboard.twig', [
            'lang' => Lang::getCurrent(),
            'user_email' => Session::get('user_email'),
            'wallets' => $wallets,
            'total_balance' => $totalBalance,
        ]);
    }

    private function getTwig(): Environment
    {
        $loader = new FilesystemLoader(ROOT . '/views');
        return new Environment($loader, [
            'cache' => ROOT . '/var/cache/twig',
            'debug' => ($_ENV['APP_DEBUG'] ?? false),
        ]);
    }
}
