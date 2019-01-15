<?php

use PHPUnit\Framework\TestCase;
use MyQRoomba\Libs\InputFileParser;

class InputFileParserTest extends TestCase
{

    public function testNonExistentInputFileReturnsFalse()
    {
        $filename = 'testFile.json';
        $parser = new InputFileParser();
        $result = $parser->analyzeInputFile($filename);

        $this->assertFalse($result);
    }

    public function testExistentCorrectFileHasProperty()
    {
        $filename = __DIR__ . '/../../test1.json';
        $parser = new InputFileParser();
        $result = $parser->analyzeInputFile($filename);

        $this->assertObjectHasAttribute('map', $result);
    }

    public function testExistentWrongFileReturnsFalse()
    {
        $filename = __DIR__ . '/../../test4.json';
        $parser = new InputFileParser();
        $result = $parser->analyzeInputFile($filename);

        $this->assertFalse($result);
    }
}