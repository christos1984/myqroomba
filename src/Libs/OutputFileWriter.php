<?php

namespace MyQRoomba\Libs;

/**
 * Abstract class that defines the writing to file functionality
 *
 * Can be extended to include more functionality but for now,
 * there is just one abstract method write() that defines the action
 * of writing data to file.
 *
 * @author Christos Patsatzis
 */
abstract class OutputFileWriter
{
    /**
     * Main function that writes data to file
     *
     * @param string $outputFile The file that will be constructed
     *
     * @param array $data        The data that will be written to file
     */
    abstract public function write(string $outputFile, array $data);
}
