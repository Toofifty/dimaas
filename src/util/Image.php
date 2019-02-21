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
     * Create a new image from the url
     *
     * @param string $url
     */
    function __construct(string $url)
    {
        $data = file_get_contents($url);
        $this->filename = BASE_DIR . self::DIR . str_random(32) . '.png';
        file_put_contents($this->filename, $data);
        $this->info = getimagesize($this->filename);
        if (!$this->info) {
            $this->destroy();
            throw new \Error('image sucked ass');
        }
        dd($this->width());
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

    function destroy(): void
    {
        unlink($this->filename);
    }
}