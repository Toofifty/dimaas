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
     * Image size information
     *
     * @var array
     */
    private $info;

    /**
     * Raw image data
     *
     * @var string
     */
    private $image;

    /**
     * Create a new image from the url
     *
     * @param string $url
     */
    function __construct(string $url)
    {
        $this->filename = BASE_DIR . self::DIR . md5($url) . '.png';
        if (file_exists($this->filename)) {
            $this->image = file_get_contents($this->filename);
        } else {
            $this->image = file_get_contents($url);
            file_put_contents($this->filename, $this->image);
        }
        $this->info = getimagesize($this->filename);
        if (!$this->info) {
            $this->destroy();
            throw new \Error('image sucked ass');
        }
    }

    /**
     * Get image width
     *
     * @return int
     */
    function width(): int
    {
        return $this->info['0'];
    }

    /**
     * Get image height
     *
     * @return int
     */
    function height(): int
    {
        return $this->info['1'];
    }

    function base64(): string
    {
        return base64_encode($this->image);
    }

    function destroy(): void
    {
        unlink($this->filename);
    }
}