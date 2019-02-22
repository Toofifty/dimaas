<?php

namespace DIM\Controller;

use DIM\Util\Template;

/**
 * Controller for responding with html examples
 */
class ExampleController
{
    public function execute(array $params)
    {
        $device = $params['device'];

        $template = file_get_contents(BASE_DIR . "/example/{$params['device']}.html");

        return Template::compile($template, ['base_url' => getenv('BASE_URL')]);
    }
}