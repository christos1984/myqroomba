<?php

namespace MyQRoomba\Entities;

class Room
{
    private $matrix;

    const CLEANABLE = 'S';
    const WALL = 'null';
    const COLUMN = 'C';

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
        if (isset($this->matrix["$dimensionX"]["$dimensionY"])) {
            if ((self::WALL !== $this->matrix["$dimensionX"]["$dimensionY"]) && (self::COLUMN !== $this->matrix["$dimensionX"]["$dimensionY"])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
