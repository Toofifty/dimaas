<?php

use DIM\Util\Router;
use DIM\Controller\SmartController;

/**
 * API endpoint definitions
 */

Router::group('v1', function () {
    Router::any('smart', SmartController::class);
});