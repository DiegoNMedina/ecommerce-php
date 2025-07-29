<?php

require_once dirname(__DIR__) . '/php/Core/ClassLoader.php';

use Core\ClassLoader;
use Core\Router;
use Controllers\HomeController;
use Controllers\ProductController;

// Registrar el autoloader
ClassLoader::register();

// Registrar los namespaces
ClassLoader::addNamespace('Core', dirname(__DIR__) . '/php/Core');
ClassLoader::addNamespace('Controllers', dirname(__DIR__) . '/php/Controllers');
ClassLoader::addNamespace('Models', dirname(__DIR__) . '/php/Models');
ClassLoader::addNamespace('Install', dirname(__DIR__) . '/install');

// Configurar manejo de errores
if (\Install\Config::DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Inicializar el router con el basePath correcto
$router = new Router('/ecommerce-php/public_html');

// Definir rutas
// Rutas de Home
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact', [HomeController::class, 'contact']);
$router->get('/calculator', [HomeController::class, 'installmentCalculator']);
$router->post('/calculator', [HomeController::class, 'installmentCalculator']);

// Rutas de Productos
$router->get('/products', [ProductController::class, 'index']);
$router->get('/product/{id}', [ProductController::class, 'show']);
$router->get('/category/{id}', [ProductController::class, 'category']);
$router->get('/search', [ProductController::class, 'search']);
$router->get('/featured', [ProductController::class, 'featured']);
$router->get('/best-selling', [ProductController::class, 'bestSelling']);
$router->post('/product/{id}/comment', [ProductController::class, 'addComment']);
$router->post('/product/{id}/like', [ProductController::class, 'like']);

// Manejar la solicitud
try {
    $router->dispatch();
} catch (Exception $e) {
    if (\Install\Config::DEBUG_MODE) {
        throw $e;
    }

    http_response_code(500);
    require __DIR__ . '/views/errors/500.php';
}
