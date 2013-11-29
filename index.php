<?php

use Mihaeu\Odin;

require __DIR__.'/vendor/autoload.php';

$odin = new Odin();
$files = $odin->findResources();
$odin->parse($files);
$odin->build();
