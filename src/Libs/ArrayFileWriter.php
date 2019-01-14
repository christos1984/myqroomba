<?php
namespace MyQRoomba\Libs;

class ArrayFileWriter extends OutputFileWriter
{
    public function write(string $outputFile, array $data){

        var_dump($data['cleaned']);
        //file_put_contents($outputFile, var_dump(array_multisort($data['cleaned'], SORT_ASC)));
        die;
    }
}