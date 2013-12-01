<?php

require __DIR__.'/vendor/autoload.php';

$odin = new Mihaeu\Odin\Odin();
$config = $odin['config'];

$userResourcePath = $odin['base.dir'].'/'.$config->get('resource_folder');
$themeResourcePath = $odin['base.dir'].'/'.$config->get('theme_folder').'/'.$config->get('theme_resource_folder');
$systemResourcePath = $odin['base.dir'].'/'.$config->get('system_resource_folder');

$userResources = $odin['locator']->locate($userResourcePath);
$themeResources = $odin['locator']->locate($themeResourcePath);
$systemResources = $odin['locator']->locate($systemResourcePath);

$parsedUserResources = $odin['parser']->parseAll($userResources);
$parsedThemeResources = $odin['parser']->parseAll($themeResources);
$parsedSystemResources = $odin['parser']->parseAll($systemResources);

//$transformedUserResources = $odin['transformer']->transformAll($parsedUserResources);
//$transformedThemeResources = $odin['transformer']->transformAll($parsedThemeResources);
//$transformedSystemResources = $odin['transformer']->transformAll($parsedSystemResources);
//
//$odin['writer']->writeAll($transformedSystemResources);
//$odin['writer']->writeAll($transformedThemeResources);
//$odin['writer']->writeAll($transformedUserResources);
