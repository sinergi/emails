<?php

if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    require_once __DIR__ . "/../vendor/autoload.php";
} elseif (file_exists(__DIR__ . "/../../autoload.php")) {
    require_once __DIR__ . "/../../autoload.php";
}

use Symfony\Component\Console\Application;

$app = new Application();

$app->addCommands([
    new \Smart\EmailQueue\Command\SendCommand(new \Smart\EmailQueue\Dispatcher())
]);

$app->run();
