<?php
namespace MyQRoomba\Core;

use MyQRoomba\Libs\InputFileParser;
use MyQRoomba\Entities\Room;
use MyQRoomba\Entities\Robot;
use MyQRoomba\Libs\JSONFileWriter;
use MyQRoomba\Libs\OutputFileWriter;
use MyQRoomba\Libs\ArrayTransposer;


class Application
{
    private $inputFile;

    private $outputFile;

    private $parser;

    private $fileWriter;

    private $robot;

    public function __construct(string $inputFile, string $outputFile, InputFileParser $parser, Room $room, Robot $robot, OutputFileWriter $fileWriter)
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->parser = $parser;
        $this->room = $room;
        $this->fileWriter= $fileWriter;

        /**
         * MyQRoomba\Entities\Robot;
         */
        $this->robot = $robot;
        $this->robot->setCostOfOperation([
            'TR' => 1,
            'TL' => 1,
            'A'  => 2,
            'B'  => 3,
            'C'  => 5
        ]);

        $this->robot->setBackOffStrategy([
            ['TR', 'A'],
            ['TL', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
            ['TR', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
        ]);
        //$this->parseInputFile($parser);
    }

    public function run()
    {
        $data = $this->parser->analyzeInputFile($this->inputFile);
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

    private function transposeData(array $array)
    {
        $retData = array();

        foreach ($array as $row => $columns) {
            foreach ($columns as $row2 => $column2) {
                $retData[$row2][$row] = $column2;
            }
        }
        return $retData;
    }





}

