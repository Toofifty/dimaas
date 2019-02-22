<?php

/**
 * Respond custom data at any time
 *
 * @param mixed $data
 * @param integer $responseCode
 * @return void
 */
function respond($data, int $responseCode = 200): void
{
    http_response_code($responseCode);
    dd($data, false, false);
}

/**
 * Validate that the required options are in the data
 *
 * @param mixed $data
 * @param string|string[] $property(s)
 * @param string $dataName Name to show in errors, default to 'data'
 * @return void
 */
function validate($properties, $data = null, string $dataName = 'data'): void
{
    if (!is_array($properties)) {
        $properties = [$properties];
    }
    foreach ($properties as $property) {
        if (($data !== null && !property_exists($data, $property)
            || ($data === null && param($property) === null))) {
            respond([
                'status' => 'error',
                'message' => "Property '$property' is missing in request $dataName"
            ], 400);
        }
    }
}

/**
 * Validate that one of the options are in the data
 *
 * @param string[] $properties
 * @param mixed $data
 * @param string $dataName Name to show in errors, default to 'data'
 * @return void
 */
function validateOneOf(array $properties, $data = null, string $dataName = 'data'): void
{
    $exists = false;
    foreach ($properties as $property) {
        if (($data !== null && property_exists($data, $property))
            || ($data === null && param($property) !== null)) {
            $exists = true;
        }
    }
    if (!$exists) {
        respond([
            'status' => 'error',
            'message' => "One of the following properties is required in request $dataName:"
                . implode(', ', $properties)
        ], 400);
    }
}


/**
 * Convert any data to string (pretty JSON if possible)
 *
 * @param mixed $data Data to stringify
 * @param boolean $pretty Use JSON_PRETTY_PRINT (defaults to true)
 * @return string
 */
function stringify($data, bool $pretty = true): string
{
    if (is_string($data)) {
        return $data;
    }
    return json_encode($data, $pretty ? JSON_PRETTY_PRINT : 0);
}

/**
 * Dump json_encoded data
 *
 * @param mixed $data
 * @param boolean $pretty Prettify JSON
 * @param boolean $pre Surround with <pre> tags
 * @return void
 */
function dump($data, bool $pretty = true, bool $pre = true): void
{
    if (!is_string($data)) {
        $data = json_encode($data, $pretty ? JSON_PRETTY_PRINT : 0);
    }

    if ($pre) {
        echo "<pre>$data</pre>";
    } else {
        echo $data;
    }
}

/**
 * Dump and die
 *
 * @param mixed $data
 * @param boolean $pretty Prettify JSON
 * @param boolean $pre Surround with <pre> tags
 * @return void
 */
function dd($data, bool $pretty = true, bool $pre = true): void
{
    dump($data, $pretty, $pre);
    die;
}

/**
 * Retrieve $_GET or $_POST value via key
 *
 * @param string $key
 * @param mixed $default value to be returned if key isn't set
 * @return mixed value or $default
 */
function param(string $key, $default = null)
{
    if (key_exists($key, $_GET)) {
        return $_GET[$key];
    }
    if (key_exists($key, $_POST)) {
        return $_POST[$key];
    }
    return $default;
}

/**
 * Generate a random string
 *
 * @param int $length
 * @return void
 */
function str_random(int $length): string
{
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}

function dist(int $x1, int $y1, int $x2, int $y2): int
{
    return (($x1 - $x2) * ($x1 - $x2) + ($y1 - $y2) * ($y1 - $y2));
}