<?php

namespace MyQRoomba\Entities;

class Room
{
    private $matrix;

    const CLEANABLE = 'S';
    const WALL      = "null";
    const COLUMN    = 'C';

    public function setMatrix($matrix)
    {
        $this->matrix = $matrix;
        return $this;
    }

    public function getMatrix()
    {
        return $this->matrix;
    }

    public function isCellVisitable(string $dimensionX, string $dimensionY)
    {
        if (isset($this->matrix["$dimensionX"]["$dimensionY"]))
        {
            if (($this->matrix["$dimensionX"]["$dimensionY"] !== self::WALL) && ($this->matrix["$dimensionX"]["$dimensionY"] !== self::COLUMN))
            {
                return true;
            }
            else return false;
        }
    }

}