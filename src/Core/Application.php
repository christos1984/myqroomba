<?php

namespace MyQRoomba\Core;

use MyQRoomba\Libs\InputFileParser;
use MyQRoomba\Entities\Room;
use MyQRoomba\Entities\Robot;
use MyQRoomba\Libs\OutputFileWriter;
use MyQRoomba\Libs\ArrayTransposer;

/**
 * Main Application class. Bootstraping everything, initializing private
 * variables in the constructor and exposing a run() method that executes the program
 *
 * Notice: An instance of this class is created by using Auryn DI Container.
 * The constructor parameters are created automatically via the DI container using Reflection,
 * achieving true DI and not implementation of Service Locator (anti)pattern
 * @see https://packagist.org/packages/rdlowrey/auryn
 *
 *
 * @author Christos Patsatzis
 */
class Application
{
    /**
     * @var string
     */
    private $inputFile;

    /**
     * @var string
     */
    private $outputFile;

    /**
     * @var MyQRoomba\Libs\InputFileParser
     */
    private $parser;

    /**
     * @var MyQRoomba\Libs\mOutputFileWriter
     */
    private $fileWriter;

    /**
     * @var MyQRoomba\Entities\Robot
     */
    private $robot;

    /**
     * Constructor method
     */
    public function __construct(string $inputFile, string $outputFile, InputFileParser $parser, Room $room, Robot $robot, OutputFileWriter $fileWriter)
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->parser = $parser;
        $this->room = $room;
        $this->fileWriter = $fileWriter;
        $this->robot = $robot;

        $this->robot->setCostOfOperation([
            'TR' => 1,
            'TL' => 1,
            'A' => 2,
            'B' => 3,
            'C' => 5,
        ]);

        $this->robot->setBackOffStrategy([
            ['TR', 'A'],
            ['TL', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
            ['TR', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
        ]);

    }

    /**
     * Actual execution of the program. Program flow is
     * a) analyze the input file using the parser and get the important info
     * b) set the room matrix equal to the data from input
     * c) get the commands that would be executed
     * d) initialize the position, direction and battery
     * e) execute the commands
     * f) write the data to output file via the OutputWriter
     *
     *
     */
    public function run()
    {
        $data = $this->parser->analyzeInputFile($this->inputFile);
        if ($data == true) {
            $this->room->setMatrix(ArrayTransposer::transposeArray($data->map));
            $this->robot->setRoom($this->room);
            $commands = $data->commands;

            $this->robot->currentXPosition = $data->start->X;
            $this->robot->currentYPosition = $data->start->Y;
            $this->robot->currentDirection = $data->start->facing;

            $this->robot->battery = $data->battery;

            $result = $this->robot->executeCommandSequence($commands);

            $this->fileWriter->write($this->outputFile, $result);
        }
        else {
            echo "not valid file provided";
        }

    }
}
