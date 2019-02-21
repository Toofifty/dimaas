<?php

use DIM\Util\Router;
use DIM\Controller\SmartController;

/**
 * API endpoint definitions
 */

Router::group('v1', function () {
    Router::on('smart', SmartController::class, 'fromImage');
    Router::on('smart/(?<both>\d+)', SmartController::class, 'fromSize');
    Router::on('smart/(?<width>\d+)x(?<height>\d+)', SmartController::class, 'fromSize');
});