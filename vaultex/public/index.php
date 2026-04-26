<?php

/**
 * Vaultex - Cryptocurrency Wallet Platform
 * Front Controller
 */

declare(strict_types=1);

// Define root path
define('ROOT', dirname(__DIR__));

// Load environment variables (simple implementation)
$envFile = ROOT . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Error reporting based on environment
if ($_ENV['APP_DEBUG'] ?? false) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Autoload
require_once ROOT . '/vendor/autoload.php';

// Import classes
use App\Core\Session;
use App\Core\Lang;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\LangController;
use App\Middleware\CsrfMiddleware;

// Initialize session
Session::init();

// Generate CSRF token for forms
CsrfMiddleware::getToken();

// Load language
$lang = Session::get('lang', 'ru');
Lang::load($lang);

// Setup router
$router = new Router();

// Public routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Language switch
$router->post('/lang/set', [LangController::class, 'set']);

// Protected routes (require auth)
// Note: Auth check is done inside controllers for simplicity
$router->get('/dashboard', [DashboardController::class, 'index']);

// Dispatch
$uri = $_GET['url'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'];

try {
    $router->dispatch($uri, $method);
} catch (\Exception $e) {
    if ($_ENV['APP_DEBUG'] ?? false) {
        echo "<pre>Error: " . htmlspecialchars($e->getMessage()) . "\n";
        echo "File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</pre>";
    } else {
        http_response_code(500);
        echo "Internal Server Error";
    }
}
