<?php

use PHPUnit\Framework\TestCase;
use MyQRoomba\Entities\Room;
use MyQRoomba\Libs\ArrayTransposer;

class RoomTest extends TestCase
{
    public function testThatCellOutsideOfRoomDimensionsReturnsFalse()
    {
        $roomMatrix =
        [
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"]
        ];

        $room = new Room();
        $room->setMatrix(ArrayTransposer::transposeArray($roomMatrix));
        $this->assertFalse($room->isCellVisitable(6,5));
    }

    public function testThatCellMatchingAWallOrColumnCannotBeVisited()
    {
        $roomMatrix =
        [
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"]
        ];

        $room = new Room();
        $room->setMatrix(ArrayTransposer::transposeArray($roomMatrix));
        $this->assertFalse($room->isCellVisitable(1,3));
    }

    public function testThatNormalCellCanBeVisited()
    {
        $roomMatrix =
        [
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"]
        ];

        $room = new Room();
        $room->setMatrix(ArrayTransposer::transposeArray($roomMatrix));
        $this->assertTrue($room->isCellVisitable(1,1));
    }
}