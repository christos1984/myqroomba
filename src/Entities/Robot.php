<?php

namespace MyQRoomba\Entities;

class Robot
{
    private $room;

    public $currentXPosition;
    public $currentYPosition;
    public $currentDirection;

    public $battery;

    public $costOfOperation;

    private $backoffStrategy;

    private $executionResult = [];

    public function __construct()
    {
        $this->costOfOperation = [
            'TR' => 1,
            'TL' => 1,
            'A'  => 2,
            'B'  => 3,
            'C'  => 5
        ];

        $this->backoffStrategy = [
            ['TR', 'A'],
            ['TL', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
            ['TR', 'B', 'TR', 'A'],
            ['TL', 'TL', 'A'],
        ];
    }



    public function setRoom($room)
    {
        $this->room = $room;
    }

    public function getRoom()
    {
        return $this->room;
    }

    public function checkIfEnoughBatteryForCommand(string $command)
    {

        switch ($command) {
            case 'TL':
            case 'TR':
                return ($this->battery >= $this->costOfOperation['TR']) ? true : false;
                break;

            case 'A':
                return ($this->battery >= $this->costOfOperation['A']) ? true : false;
                break;
            case 'B':
                return ($this->battery >= $this->costOfOperation['B']) ? true : false;
                break;
            case 'C':
                return ($this->battery >= $this->costOfOperation['C']) ? true : false;
                break;
        }
    }

    public function recalculateBattery(string $command)
    {
        $this->battery = $this->battery - $this->costOfOperation["$command"];
        //echo "Battery level after command $command is $this->battery \n";
    }

    public function move(string $direction)
    {
        switch ($direction) {
            case 'E':
                $newXposition = $this->currentXPosition + 1 ;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    $this->currentXPosition++;
                    $this->addCellToVisited();
                    return true;
                }
                else return false;
                break;
            case 'W':
                $newXposition = $this->currentXPosition - 1 ;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    $this->currentXPosition--;
                    $this->addCellToVisited();
                    return true;

                }
                else return false;
                break;
            case 'S':
                $newYposition = $this->currentYPosition + 1 ;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    $this->currentYPosition++;
                    $this->addCellToVisited();
                    return true;
                }
                else return false;
                break;
            case 'N':
                $newYposition = $this->currentYPosition - 1 ;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    $this->currentYPosition--;
                    $this->addCellToVisited();
                    return true;
                }
                else return false;
                break;
            default:
                return true;
                break;
        }
    }

    public function initiateBackOffStrategy()
    {
        foreach ($this->backoffStrategy as $strategySteps)
        {

            if ($this->executeBackOffCommandSequence($strategySteps)) {
                break;
            }

        }
    }


    public function back(string $direction)
    {
        switch ($direction) {
            case 'E':
                $newXposition = $this->currentXPosition - 1 ;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    $this->currentXPosition--;
                    $this->addCellToVisited();
                }
                else return false;
                break;
            case 'W':
                $newXposition = $this->currentXPosition + 1 ;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    $this->currentXPosition--;
                    $this->addCellToVisited();
                }
                else return false;
                break;
            case 'S':
                $newYposition = $this->currentYPosition - 1 ;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    $this->currentYPosition--;
                    $this->addCellToVisited();
                }
                else return false;
                break;
            case 'N':
                $newYposition = $this->currentYPosition + 1 ;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    $this->currentYPosition++;
                    $this->addCellToVisited();
                }
                else return false;
                break;
            default:
                return true;
                break;
        }
    }




    public function executeBackOffCommandSequence(array $strategySteps)
    {

        $status = true;
        //var_dump($strategySteps);die;
        // start with the first set
        foreach ($strategySteps as $command) {
            // echo "Executing backoff command $command \n";
            if ($this->checkIfEnoughBatteryForCommand($command))
            {
                switch ($command) {
                    case 'TL':
                    case 'TR':
                        $this->changeDirection($command);
                        $this->recalculateBattery($command);
                        break;

                    case 'A':
                        $this->recalculateBattery($command);
                        if ($this->move($this->currentDirection)) {
                            break;
                        } else $status = false;
                        break;
                    case 'B':
                          $this->recalculateBattery($command);
                          if ($this->back($this->currentDirection)) {


                            break;
                        } else $status = false;
                        break;
                    case 'C':
                        $this->addCellToCleaned();
                        $this->recalculateBattery($command);
                        break;
                }

            }
            else {
                break;
            }

        }
        return $status;
    }

    public function executeCommand(string $command)
    {
        if ($this->checkIfEnoughBatteryForCommand($command))
        {
            switch ($command) {
                case 'TL':
                case 'TR':
                    $this->changeDirection($command);
                    $this->recalculateBattery($command);
                    return true;
                    break;

                case 'A':
                    $this->recalculateBattery($command);
                    if ($this->move($this->currentDirection)) {

                        return true;
                        break;
                    } else $this->initiateBackOffStrategy();
                case 'B':
                    return ($this->battery >= $this->costOfOperation['B']) ? true : false;
                    break;
                case 'C':
                    $this->addCellToCleaned();
                    $this->recalculateBattery($command);
                    return true;
                    break;
            }
        }
        else {
            die('battery off');
        }
    }

    public function executeCommandSequence(array $commands){

        // irregardless of the command, we are going to have to add the current
        // cell to visited ones
        $this->addCellToVisited();
        foreach ($commands as $command) {
            $this->executeCommand($command);
        }
        return $this->returnResults();
    }

    public function returnResults()
    {

        $this->executionResult['final']['X'] = $this->currentXPosition;
        $this->executionResult['final']['Y'] = $this->currentYPosition;
        $this->executionResult['final']['facing'] = $this->currentDirection;
        $this->executionResult['battery'] = $this->battery;
        array_multisort($this->executionResult['visited'], SORT_ASC);
        array_multisort($this->executionResult['cleaned'], SORT_ASC);
        return $this->executionResult;
    }

    private function changeDirection(string $command){
        switch ($command) {
            case 'TL':
                if ($this->currentDirection === 'E') {
                    $this->currentDirection = 'N';
                }
                else if ($this->currentDirection === 'N') {
                    $this->currentDirection = 'W';
                }
                else if ($this->currentDirection === 'W') {
                    $this->currentDirection = 'S';
                }
                else if ($this->currentDirection === 'S') {
                    $this->currentDirection = 'E';
                }
                break;
            case 'TR':
                if ($this->currentDirection === 'E') {
                    $this->currentDirection = 'S';
                }
                else if ($this->currentDirection === 'N') {
                    $this->currentDirection = 'E';
                }
                else if ($this->currentDirection === 'W') {
                    $this->currentDirection = 'N';
                }
                else if ($this->currentDirection === 'S') {
                    $this->currentDirection = 'W';
                }
                break;
        }
    }

    public function addCellToVisited()
    {
        if (!$this->isCellVisited($this->currentXPosition, $this->currentYPosition))
        $this->executionResult['visited'][] = ['X' => $this->currentXPosition, 'Y' => $this->currentYPosition];
    }

    public function addCellToCleaned()
    {
        if (!$this->isCellCleaned($this->currentXPosition, $this->currentYPosition))
        $this->executionResult['cleaned'][] = ['X' => $this->currentXPosition, 'Y' =>  $this->currentYPosition];
        //array_push($this->executionResult['visited']    , [$this->currentXPosition, $this->currentYPosition]);
        //$this->executionResult = array_unique($this->executionResult);
    }


    private function isCellVisited($x, $y)
    {
        if (isset($this->executionResult['visited'])){
            foreach ($this->executionResult['visited'] as $cell)
                if (($cell["X"] == $x) && ($cell["Y"] == $y)) return true;
        }
        return false;
    }

    private function isCellCleaned($x, $y)
    {
        if (isset($this->executionResult['cleaned'])){
            foreach ($this->executionResult['cleaned'] as $cell)
                if (($cell["X"] == $x) && ($cell["Y"] == $y)) return true;
        }
        return false;
    }
}