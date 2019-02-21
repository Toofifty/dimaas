<?php

namespace DIM\Util;

class Router
{
    private static $routes = [];
    private static $groups = [];

    /**
     * Group requests in a namespace
     *
     * @param string $group
     * @param callable $func
     * @return void
     */
    public static function group(string $group, callable $func): void
    {
        self::$groups[] = $group;
        $func();
        array_pop(self::$groups);
    }

    /**
     * Register a route
     *
     * @param string $method
     * @param string $path
     * @param string $class
     * @return void
     */
    public static function on(string $path, string $class, string $func = 'execute'): void
    {
        $groups = implode('/', self::$groups);
        $path = self::slash("$groups/$path");
        self::$routes["^{$path}$"] = [$class, $func];
    }

    /**
     * Parse and clean a URL
     *
     * @param string $url
     * @return string
     */
    private static function clean(string $url): string
    {
        return self::slash(parse_url($url, PHP_URL_PATH));
    }

    /**
     * Add a trailing slash to a URL if not already present
     *
     * @param string $url
     * @return string
     */
    private static function slash(string $url): string
    {
        if (strpos($url, '/') !== 0) {
            $url = "/$url";
        }
        if (substr($url, strlen($url) - 2) !== '/') {
            $url .= '/';
        }
        return $url;
    }

    /**
     * Define JSON response headers
     *
     * @return void
     */
    private static function headers(): void
    {
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET,POST,OPTIONS');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
            header('Content-Type: application/json;charset=utf-8');
        }
    }

    /**
     * Get the route class at the path
     *
     * @param string $method
     * @param string $path
     * @return array
     */
    private static function getRoute(string $path): array
    {
        $matches = [];
        foreach (self::$routes as $route => $action) {
            if (preg_match_all('|' . $route . '|', $path, $matches)) {
                // remove full pattern
                array_shift($matches);
                // get value of each match
                return [$action, array_map(function ($match) {
                    return $match[0];
                }, $matches)];
            }
        }
        respond([
            'status' => 'error',
            'message' => '404 resource not found'
        ], 404);
    }

    /**
     * Execute a route
     *
     * @param string $method
     * @param string $path
     * @return void
     */
    public static function execute(string $path)
    {
        // correct POST for JSON
        $_POST = json_decode(file_get_contents('php://input'), true);

        [[$class, $func], $params] = self::getRoute(self::clean($path));

        (new $class)->$func($params);
    }
}