<?php
// config/routes.php
declare(strict_types=1);

use App\Core\Router;
use App\Controllers\{
    AuthController,
    DashboardController,
    ServiceController,
    SwapController,
    MessageController
};

$router = new Router();

// ───────────────────────────────────────────────
// Authentication
// ───────────────────────────────────────────────
$router->get('/login',      [AuthController::class, 'showLogin']);
$router->post('/login',     [AuthController::class, 'login']);

$router->get('/register',   [AuthController::class, 'showRegister']);
$router->post('/register',  [AuthController::class, 'register']);

$router->post('/logout',    [AuthController::class, 'logout']);

// ───────────────────────────────────────────────
// Home / Browse
// ───────────────────────────────────────────────
$router->get('/',               [ServiceController::class, 'index']);
$router->get('/services',       [ServiceController::class, 'index']);
$router->get('/services/:id',   [ServiceController::class, 'show']);

// ───────────────────────────────────────────────
// Service CRUD
// ───────────────────────────────────────────────
$router->post('/services',             [ServiceController::class, 'create']);
$router->post('/services/:id/edit',    [ServiceController::class, 'update']);
$router->post('/services/:id/delete',  [ServiceController::class, 'delete']);

// ───────────────────────────────────────────────
// Dashboard
// ───────────────────────────────────────────────
$router->get('/dashboard',         [DashboardController::class, 'index']);
$router->get('/profile',           [DashboardController::class, 'profile']);
$router->post('/profile/update',   [DashboardController::class, 'updateProfile']);
$router->get('/users/:id',         [DashboardController::class, 'viewUser']);

// ───────────────────────────────────────────────
// Swaps
// ───────────────────────────────────────────────
$router->post('/swaps/request',        [SwapController::class, 'request']);
$router->get('/swaps/:id',             [SwapController::class, 'show']);
$router->post('/swaps/:id/accept',     [SwapController::class, 'accept']);
$router->post('/swaps/:id/decline',    [SwapController::class, 'decline']);
$router->post('/swaps/:id/complete',   [SwapController::class, 'complete']);
$router->post('/swaps/:id/review',     [SwapController::class, 'review']);

// ───────────────────────────────────────────────
// Messages
// ───────────────────────────────────────────────
$router->get('/messages',              [MessageController::class, 'inbox']);
$router->get('/messages/:swap_id',     [MessageController::class, 'conversation']);
$router->post('/messages/send',        [MessageController::class, 'send']);

// ───────────────────────────────────────────────
// Subscriptions
// ───────────────────────────────────────────────
$router->get('/subscriptions', [DashboardController::class, 'subscriptions']);

// =====================================================
// DEBUG OUTPUT (TEMPORARY)
// =====================================================

echo "<pre>";

echo "REQUEST_METHOD : " . ($_SERVER['REQUEST_METHOD'] ?? '') . PHP_EOL;
echo "REQUEST_URI    : " . ($_SERVER['REQUEST_URI'] ?? '') . PHP_EOL;
echo "SCRIPT_NAME    : " . ($_SERVER['SCRIPT_NAME'] ?? '') . PHP_EOL;
echo "PHP_SELF       : " . ($_SERVER['PHP_SELF'] ?? '') . PHP_EOL;
echo "DOCUMENT_ROOT  : " . ($_SERVER['DOCUMENT_ROOT'] ?? '') . PHP_EOL;
echo "APP_BASE       : " . (defined('APP_BASE') ? APP_BASE : 'NOT DEFINED') . PHP_EOL;

echo PHP_EOL;
echo "SERVER VARIABLES" . PHP_EOL;
echo "==============================" . PHP_EOL;

print_r($_SERVER);

exit;

// =====================================================
// NORMAL DISPATCH (restore after debugging)
// =====================================================

// $method = $_SERVER['REQUEST_METHOD'];
// $uri    = $_SERVER['REQUEST_URI'];
// $router->dispatch($method, $uri);