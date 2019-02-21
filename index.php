<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/api.php';

use DIM\Util\Router;

define('BASE_DIR', __DIR__);

try {
    Router::execute($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
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
