<?php

use Mihaeu\Odin\Console\Application;
use Mihaeu\Odin\Console\GenerateCommand;
use Mihaeu\Odin\Console\NewCommand;

require __DIR__.'/../vendor/autoload.php';

$application = new Application();
$application->add(new GenerateCommand());
$application->add(new NewCommand());
$application->run();
