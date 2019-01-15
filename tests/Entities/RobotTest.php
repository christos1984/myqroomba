<?php

use PHPUnit\Framework\TestCase;
use MyQRoomba\Entities\Robot;
use MyQRoomba\Entities\Room;

class RobotTest extends TestCase
{

    private function initializeRobot()
    {
        $robot = new Robot();
        $robot->setCostOfOperation([
            'TR' => 1,
            'TL' => 1,
            'A'  => 2,
            'B'  => 3,
            'C'  => 5
        ]);

        $robot->setBackOffStrategy([
            ['TR', 'A'],
            ['TL', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
            ['TR', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
        ]);
        return $robot;
    }

    public function testThatCommandFailsIfBatteryIsNotEnough()
    {
        $robot = $this->initializeRobot();
        $robot->battery = 2;
        $this->assertFalse($robot->checkIfEnoughBatteryForCommand('C'));
    }

    public function testThatCommandSucceedsIfBatteryIsEnough()
    {
        $robot = $this->initializeRobot();
        $robot->battery = 23;
        $this->assertTrue($robot->checkIfEnoughBatteryForCommand('C'));
    }

    public function testThatSeriesOfCommandsAreDrainingTheBatteryCorrectly()
    {
        $robot = $this->initializeRobot();
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
        $robot = $this->initializeRobot();
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