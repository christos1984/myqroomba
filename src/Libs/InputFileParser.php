<?php

namespace MyQRoomba\Libs;

/**
 * Helps analyze the file from input, verify and extract the
 * contents of it as a PHP Object.
 *
 *
 * @author Christos Patsatzis
 */
class InputFileParser
{
    public function analyzeInputFile(string $filename)
    {
        if (file_exists($filename)) {
            if ($this->isValidRoombaInstructionFile($filename)) {
                $input = file_get_contents($filename);
                return json_decode($input);
            }
            else return false;
        } else {
            return false;
        }
    }

    /**
     * Analyzes a given file and determine if is it valid to be loaded into the robot.
     *
     * @param string $filename Name of the file
     *
     * @return bool
     */
    public function isValidRoombaInstructionFile(string $filename)
    {
        $input = file_get_contents($filename);
        if ($this->isJson($input)) {
            $decoded = json_decode($input);
            if ((property_exists($decoded, 'map')) &&
                (property_exists($decoded, 'start')) &&
                (property_exists($decoded, 'commands')) &&
                (property_exists($decoded, 'battery'))) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function isJson(string $string)
    {
        json_decode($string);

        return JSON_ERROR_NONE == json_last_error();
    }
}
