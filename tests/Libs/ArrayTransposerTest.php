<?php

use PHPUnit\Framework\TestCase;
use MyQRoomba\Libs\ArrayTransposer;

class ArrayTransposerTest extends TestCase
{
    public function testThatArrayIsTransposedCorrectly()
    {
        $arr1 = [
            [ 1, 3],
            [ 2, 4],
        ];

        $expected = [
            [1, 2],
            [3, 4]
        ];

        $this->assertEquals($expected, ArrayTransposer::transposeArray($arr1));
    }
}