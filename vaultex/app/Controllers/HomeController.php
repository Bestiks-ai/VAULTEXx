<?php

namespace App\Controllers;

use App\Core\Lang;
use App\Models\Banner;
use App\Models\Transaction;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeController
{
    public function index(): void
    {
        $bannerModel = new Banner();
        $transactionModel = new Transaction();

        $banners = $bannerModel->getAllActive();
        
        // Get stats for hero section
        $stats = [
            'users' => 10000, // Placeholder - should come from DB
            'transactions' => $transactionModel->getTotalCount(),
            'countries' => 50,
        ];

        $twig = $this->getTwig();
        echo $twig->render('home.twig', [
            'lang' => Lang::getCurrent(),
            'banners' => $banners,
            'stats' => $stats,
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
