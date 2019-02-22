<?php

namespace DIM\Controller;

use DIM\Util\Image;
use DIM\Util\Template;
use DIM\DevicePicker;

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
        validate('image');

        $params['width'] = $params['width'] ?? $params['both'];
        $params['height'] = $params['height'] ?? $params['both'];
        $params['image_url'] = param('image');
        $params['inline'] = param('inline');

        header('Content-Type: image/svg+xml');

        if (!$params['inline']) {
            $image = new Image(param('image'));
            $params['image'] = $image->base64();
        }

        return Template::compile($this->pickTemplate($params), $params);
    }

    private function pickTemplate(array $options): string
    {
        return (new DevicePicker)->pick($options);
    }
}