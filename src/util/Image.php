<?php

namespace DIM\Util;

class Image
{
    /**
     * Directory to store files
     */
    private const DIR = '/tmp/';

    /**
     * Temporary file name
     *
     * @var string
     */
    private $filename;

    /**
     * Create a new image from the url
     *
     * @param string $url
     */
    function __construct(string $url)
    {
        $data = file_get_contents($url);
        $this->filename = BASE_DIR . self::DIR . str_random(32) . '.png';
        file_put_contents($this->filename, $data);
        $info = getimagesize($this->filename);
        if (!$info) {
            $this->destroy();
            throw new \Error('image sucked ass');
        }
    }

    function destroy()
    {
        unlink($this->filename);
    }
}