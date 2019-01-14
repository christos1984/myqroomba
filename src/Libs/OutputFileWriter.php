<?php
namespace MyQRoomba\Libs;

abstract class OutputFileWriter
{
    abstract public function write(string $outputFile, array $data);
}