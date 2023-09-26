<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class Devices
{
    use Nette\SmartObject;

    public $devices;

    public function __construct()
    {
        $this->devices = [];
    }

    public function add(Device $device): void
    {
        $this->devices[$device->attrs['id']] = $device;
    }

    public function get(int $id): Device
    {
        return $this->devices[$id];
    }
}
