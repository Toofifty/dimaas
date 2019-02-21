<?php

namespace DIM\Controller;

use DIM\Util\Image;

class SmartController
{
    public function fromImage(array $params)
    {
        validate('image');

        $image = new Image(param('image'));

        $image->destroy();
    }

    public function fromSize(array $params)
    {
        $width = $params['width'] ?? $params['both'];
        $height = $params['height'] ?? $params['both'];

        // $this->pickDevice()
        // Template::compile($device, [$options])

        dd([$width, $height]);
    }
}