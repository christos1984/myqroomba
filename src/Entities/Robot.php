<?php

namespace MyQRoomba\Entities;

class Robot
{
    private $room;
    public $currentXPosition;
    public $currentYPosition;
    public $currentDirection;
    public $battery;
    private $costOfOperation;
    private $backoffStrategy;
    private $executionResult = [];

    public function setCostOfOperation(array $costOfOperation)
    {
        $this->costOfOperation = $costOfOperation;
    }

    public function getCostOfOperation()
    {
        return $this->costOfOperation;
    }

    public function setBackOffStrategy(array $backoffStrategy)
    {
        $this->backoffStrategy = $backoffStrategy;
    }

    public function getBackOffStrategy()
    {
        return $this->backOffStrategy;
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
    }

    public function move(string $direction)
    {
        switch ($direction) {
            case 'E':
                $newXposition = $this->currentXPosition + 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    ++$this->currentXPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            case 'W':
                $newXposition = $this->currentXPosition - 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    --$this->currentXPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            case 'S':
                $newYposition = $this->currentYPosition + 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    ++$this->currentYPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            case 'N':
                $newYposition = $this->currentYPosition - 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    --$this->currentYPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return true;
                break;
        }
    }

    public function initiateBackOffStrategy()
    {
        foreach ($this->backoffStrategy as $strategySteps) {
            if ($this->executeBackOffCommandSequence($strategySteps)) {
                break;
            }
        }
    }

    private function back(string $direction)
    {
        switch ($direction) {
            case 'E':
                $newXposition = $this->currentXPosition - 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    --$this->currentXPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            case 'W':
                $newXposition = $this->currentXPosition + 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    --$this->currentXPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            case 'S':
                $newYposition = $this->currentYPosition - 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    --$this->currentYPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            case 'N':
                $newYposition = $this->currentYPosition + 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    ++$this->currentYPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return true;
                break;
        }
    }

    public function executeBackOffCommandSequence(array $strategySteps)
    {
        $status = true;
        // start with the first set
        foreach ($strategySteps as $command) {
            $breakStatus = false;
            if ($this->checkIfEnoughBatteryForCommand($command)) {
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
                        } else {
                            $status = false;
                            $breakStatus = true;
                        }
                        break;
                    case 'B':
                          $this->recalculateBattery($command);
                          if ($this->back($this->currentDirection)) {
                              break;
                          } else {
                              $status = false;
                              $breakStatus = true;
                          }
                        break;
                    case 'C':
                        $this->addCellToCleaned();
                        $this->recalculateBattery($command);
                        break;
                }
            } else {
                break;
            }
            if ($breakStatus === true) break;
        }


        return $status;
    }

    public function executeCommand(string $command)
    {
        if ($this->checkIfEnoughBatteryForCommand($command)) {
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
                    } else {
                        $this->initiateBackOffStrategy();
                    }
                    // no break
                case 'B':
                    return ($this->battery >= $this->costOfOperation['B']) ? true : false;
                    break;
                case 'C':
                    $this->addCellToCleaned();
                    $this->recalculateBattery($command);

                    return true;
                    break;
            }
        } else {
            die('battery off');
        }
    }

    public function executeCommandSequence(array $commands)
    {
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
        if (isset($this->executionResult['visited'])) {
            array_multisort($this->executionResult['visited'], SORT_ASC);
        }
        if (isset($this->executionResult['cleaned'])) {
            array_multisort($this->executionResult['cleaned'], SORT_ASC);
        }

        return $this->executionResult;
    }

    private function changeDirection(string $command)
    {
        switch ($command) {
            case 'TL':
                if ('E' === $this->currentDirection) {
                    $this->currentDirection = 'N';
                } elseif ('N' === $this->currentDirection) {
                    $this->currentDirection = 'W';
                } elseif ('W' === $this->currentDirection) {
                    $this->currentDirection = 'S';
                } elseif ('S' === $this->currentDirection) {
                    $this->currentDirection = 'E';
                }
                break;
            case 'TR':
                if ('E' === $this->currentDirection) {
                    $this->currentDirection = 'S';
                } elseif ('N' === $this->currentDirection) {
                    $this->currentDirection = 'E';
                } elseif ('W' === $this->currentDirection) {
                    $this->currentDirection = 'N';
                } elseif ('S' === $this->currentDirection) {
                    $this->currentDirection = 'W';
                }
                break;
        }
    }

    public function addCellToVisited()
    {
        if (!$this->isCellVisited($this->currentXPosition, $this->currentYPosition)) {
            $this->executionResult['visited'][] = ['X' => $this->currentXPosition, 'Y' => $this->currentYPosition];
        }
    }

    public function addCellToCleaned()
    {
        if (!$this->isCellCleaned($this->currentXPosition, $this->currentYPosition)) {
            $this->executionResult['cleaned'][] = ['X' => $this->currentXPosition, 'Y' => $this->currentYPosition];
        }
    }

    public function isCellVisited($x, $y)
    {
        if (isset($this->executionResult['visited'])) {
            foreach ($this->executionResult['visited'] as $cell) {
                if (($cell['X'] == $x) && ($cell['Y'] == $y)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isCellCleaned($x, $y)
    {
        if (isset($this->executionResult['cleaned'])) {
            foreach ($this->executionResult['cleaned'] as $cell) {
                if (($cell['X'] == $x) && ($cell['Y'] == $y)) {
                    return true;
                }
            }
        }

        return false;
    }
}
