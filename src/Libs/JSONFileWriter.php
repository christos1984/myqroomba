<?php
namespace MyQRoomba\Libs;

class JSONFileWriter extends OutputFileWriter
{
    public function write(string $outputFile, array $data){

        file_put_contents($outputFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}