<?php

namespace MyQRoomba\Libs;

/**
 * Class that extends OutputFileWriter to fill output data formatted
 * as JSON.
 *
 * @author Christos Patsatzis
 */
class JSONFileWriter extends OutputFileWriter
{
    /**
     * Main function that writes data to file
     *
     * @param string $outputFile   The file that will be constructed
     *
     * @param array $data          The data that will be written to file
     */
    public function write(string $outputFile, array $data)
    {
        file_put_contents($outputFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
