<?php

use PHPUnit\Framework\TestCase;
use MyQRoomba\Entities\Robot;
use MyQRoomba\Entities\Room;

class RobotTest extends TestCase
{
    public function testThatCommandFailsIfBatteryIsNotEnough()
    {
        $robot = new Robot();
        $robot->battery = 2;
        $this->assertFalse($robot->checkIfEnoughBatteryForCommand('C'));
    }

    public function testThatCommandSucceedsIfBatteryIsEnough()
    {
        $robot = new Robot();
        $robot->battery = 23;
        $this->assertTrue($robot->checkIfEnoughBatteryForCommand('C'));
    }

    public function testThatSeriesOfCommandsAreDrainingTheBatteryCorrectly()
    {
        $robot = new Robot();
        $room =  $this->getMockBuilder(Room::class)
            ->setMethods()
            ->getMock();

        $room->setMatrix([
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"]
          ]);

        $robot->setRoom($room);
        $robot->battery = 80;
        $robot->currentDirection = "N";
        $robot->currentXPosition = "3";
        $robot->currentYPosition = "0";
        $robot->executeCommandSequence([ "TL","A","C","A","C","TR","A","C"]);
        $this->assertEquals('54', $robot->battery);
    }



    public function testThatBackMakesTheRobotGoBackWhenNoObstacle()
    {
        $robot = new Robot();
        $room =  $this->getMockBuilder(Room::class)
            ->setMethods()
            ->getMock();

        $room->setMatrix([
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"]
          ]);

        $robot->setRoom($room);
        $robot->battery = 80;
        $robot->currentDirection = "N";
        $robot->currentXPosition = "3";
        $robot->currentYPosition = "0";

        //$this->assertEquals("E", $robot->changeDirection("TR"));


    }
}