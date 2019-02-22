<?php

use DIM\Util\Router;
use DIM\Controller\SmartController;
use DIM\Controller\ExampleController;

/**
 * API endpoint definitions
 */

Router::group('v1', function () {
    Router::group('svg', function () {
        Router::on('smart', SmartController::class, 'fromImage');
        Router::on('smart/(?<both>\d+)', SmartController::class, 'fromSize');
        Router::on('smart/(?<width>\d+)x(?<height>\d+)', SmartController::class, 'fromSize');
    });
    Router::group('script', function () {
        Router::on('smart', SmartController::class, 'fromImage');
        Router::on('smart/(?<both>\d+)', SmartController::class, 'fromSize');
        Router::on('smart/(?<width>\d+)x(?<height>\d+)', SmartController::class, 'fromSize');
    });
});

Router::on('example/(?<device>[\w-]+)', ExampleController::class);