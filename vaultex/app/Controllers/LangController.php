<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\Lang;

class LangController
{
    public function set(): void
    {
        $locale = $_POST['locale'] ?? 'ru';
        
        if (in_array($locale, ['ru', 'en'])) {
            Session::set('lang', $locale);
            Lang::load($locale);
        }

        // Redirect back
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit;
    }
}
