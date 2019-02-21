<?php

namespace DIM\Controller;

use DIM\Util\Image;

class SmartController
{
    public function execute(array $params)
    {
        validate('image');

        $image = new Image(param('image'));

        $image->destroy();
    }
}