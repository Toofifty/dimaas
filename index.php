<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/api.php';

use DIM\Util\Router;

define('BASE_DIR', __DIR__);

(new Symfony\Component\Dotenv\Dotenv())->load(__DIR__ . '/.env');

try {
    Router::execute($_SERVER['REQUEST_URI']);
} catch (Error $e) {
    respond([
        'status' => 'error',
        'message' => $e->getMessage(),
        'exception' => $e
    ], 500);
} catch (Exception $e) {
    respond([
        'status' => 'error',
        'message' => $e->getMessage(),
        'exception' => $e
    ], 500);
}
