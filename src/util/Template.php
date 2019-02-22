<?php

namespace DIM\Util;

/**
 * Simple template engine for inserting data into SVG and HTML templates.
 *
 * Supports:
 * - variables {{ myVar }}
 * - properties {{ myObj.var }}
 * - if/elseif/else {{ if myVar }} {{ elseif otherVar }} {{ else }} {{ endif }}
 * - default values {{ myColour | white }}
 */
class Template
{
    /**
     * The template to render
     *
     * @var string
     */
    private $template;

    /**
     * Create a new template instance
     *
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->template = $template;
    }

    /**
     * Resolve a single matched variable
     *
     * @param array $matches varMatcher matches array
     * @param array $data Render data
     * @return string resolved value
     */
    private function resolveVariable(array $matches, array $data): string
    {
        // $matches[0] is the entire match `<{key}>`
        // $matches[1] is the word inside `{<key>}`
        // $matches[2] is the default value (if exists) `{key|<default>}`
        $default = null;
        [$match, $key] = $matches;
        if (count($matches) > 2) {
            $default = $matches[2];
        }

        if (key_exists($key, $data) && $data[$key] !== null) {
            return $data[$key];
        }

        return $default ?? 'undefined';
    }

    /**
     * Resolve a single matched object property
     *
     * @param array $matches varMatcher matches array
     * @param array $data Render data
     * @return string resolved value
     */
    private function resolveProperty(array $matches, array $data): string
    {
        // $matches[0] is the entire match `<{key.prop}>`
        // $matches[1] is the first word inside `{<key>.prop}`
        // $matches[2] is the second word inside `{key.<prop>}`
        // $matches[3] is the default value (if exists) `{key.prop|<default>}`
        $default = null;
        [$match, $key, $prop] = $matches;
        if (count($matches) > 3) {
            $default = $matches[3];
        }

        if (key_exists($key, $data)) {
            $object = $data[$key];
            if ($object !== null) {
                if (is_array($object) && key_exists($prop, $object)) {
                    // return value of associative array key
                    return $object[$prop];
                } elseif (property_exists($object, $prop)) {
                    // return value of object property
                    return $object->{$prop};
                }
            }
        }

        return $default ?? 'undefined';
    }

    /**
     * Resolve logical (if) blocks
     *
     * @param string $template
     * @param array $data
     * @return string template
     */
    private function resolveBlocks(string $template, array $data): string
    {
        $stack = [];

        $matcher = '/{{\s*(if|elseif|else|endif)(?:\s+(!?[.\w]+))?\s*}}/';

        preg_replace_callback(
            $matcher,
            function ($matches) use (&$stack, &$template, $data) {
                [$fullControl, $symbol] = $matches;
                $variable = null;
                if (count($matches) > 2) {
                    $variable = $matches[2];
                }
                $index = strpos($template, $fullControl);
                $template = preg_replace("/\s*$fullControl/", '', $template, 1);

                if ($symbol === 'if') {
                    // push to stack
                    $stack[] = [$variable, $index];
                } else {
                    // pop and eval on any other symbol
                    [$prev, $prevIndex] = array_pop($stack);
                    $not = strpos($prev, '!') === 0;
                    if ($not) $prev = substr($prev, 1);
                    // if last var is false, remove block content
                    if ($not === !!$data[$prev]) {
                        $start = substr($template, 0, $prevIndex);
                        $end = substr($template, $index);
                        $template = $start . $end;
                    }
                }

                if ($symbol === 'elseif') {
                    // push new variable to stack
                    $stack[] = [$variable, $index];
                } elseif ($symbol === 'else') {
                    // push inverse variable to stack
                    $stack[] = [$not ? $prev : "!$prev", $index];
                }
            },
            $template,
            PREG_OFFSET_CAPTURE
        );

        return $template;
    }

    /**
     * Resolve variables in the string
     *
     * @param string $template
     * @param array $data
     * @return string template
     */
    private function resolveVariables(string $template, array $data): string
    {
        // regex explanation time!
        // /{{\s*([\w_-]+)(?:\s*\|\s*(\S+))?\s*}}/
        //  {{\s*                                   {{ followed by 0 or more whitespace
        //       ([\w_-]+)                          1 or word char, _ or - (and save)
        //        ^^^^^^^                               This is the variable name
        //                (?:             )?        Optional (?: means DON'T save it)
        //                   \s*\|\s*               Literal pipe with any whitespace
        //                           (\S+)          1 or more non-whitespace (and save)
        //                            ^^^               This is the default value
        //                                  \s*}}   }} preceded by 0 or more whitespace
        $matcher = '/{{\s*([\w_-]+)(?:\s*\|\s*(\S+))?\s*}}/';

        return preg_replace_callback(
            $matcher,
            function ($matches) use ($data) {
                return $this->resolveVariable($matches, $data);
            },
            $template
        );
    }

    /**
     * Resolve properties in the string
     *
     * @param string $template
     * @param array $data
     * @return string template
     */
    private function resolveProperties(string $template, array $data): string
    {
        // identical regex - it's just expecting a literal period
        // between two variable names
        $matcher = '/{{\s*([\w_-]+)\.([\w_-]+)(?:\s*\|\s*(\S+))?\s*}}/';

        return preg_replace_callback(
            $matcher,
            function ($matches) use ($data) {
                return $this->resolveProperty($matches, $data);
            },
            $template
        );
    }

    /**
     * Render data into the template
     *
     * @param array $data
     * @return string interpolated template
     */
    public function render(array $data): string
    {
        $template = $this->resolveBlocks($this->template, $data);
        $template = $this->resolveVariables($template, $data);
        $template = $this->resolveProperties($template, $data);

        return $template;
    }

    /**
     * Shortcut to create and render a template
     *
     * @param string $template
     * @param array $data
     * @return string interpolated template
     */
    public static function compile(string $template, array $data): string
    {
        return (new Template($template))->render($data);
    }
}