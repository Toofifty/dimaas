<?php

namespace DIM;

require __DIR__ . '/definitions.php';

class DevicePicker
{
    /**
     * Pick a device based on the options given
     *
     * @param array $options
     * @return string
     */
    public function pick(array $options): string
    {
        $chosen = $this->pickClosest($options);
        return file_get_contents(BASE_DIR . "/devices/{$chosen['form']}/{$chosen['name']}.svg");
    }

    public function pickClosest(array $options): array
    {
        $devices = DEVICES;
        if ($options['form']) {
            $devices = DEVICES[$options['form']];
        }
        return $this->closestSize($devices, $options['width'], $options['height']);
    }

    public function closestSize(array $devices, int $width, int $height): array
    {
        usort($devices, function ($a, $b) use ($width, $height) {
            return dist($a['width'], $a['height'], $width, $height)
                - dist($b['width'], $b['height'], $width, $height);
        });
        return $devices[0];
    }
}