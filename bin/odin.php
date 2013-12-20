<?php

use Mihaeu\Odin\Console\Application;
use Mihaeu\Odin\Console\GenerateCommand;

require __DIR__.'/../vendor/autoload.php';

$application = new Application();
$application->add(new GenerateCommand());
$application->add(new NewCommand());
$application->run();
