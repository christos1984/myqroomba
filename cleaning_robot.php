<?php

use MyQRoomba\Core\Application;

require_once __DIR__.'/vendor/autoload.php';

$inputFile = $argv['1'];
$outputFile = $argv['2'];

$injector = new Auryn\Injector();
$injector->share('MyQRoomba\Core\Application');

$injector->define('MyQRoomba\Core\Application', [':inputFile' => $inputFile, ':outputFile' => $outputFile, 'fileWriter' => 'MyQRoomba\Libs\JSONFileWriter']);

$app = $injector->make('MyQRoomba\Core\Application');
$app->run();
