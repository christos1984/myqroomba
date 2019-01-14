<?php

namespace MyQRoomba\Libs;

class InputFileParser
{
    public function __construct()
    {
    }

    public function analyzeInputFile(string $filename)
    {
        if (file_exists($filename))
        {
            $input = file_get_contents($filename);
            $decoded = json_decode($input);
            return $decoded;
        }
    }
}