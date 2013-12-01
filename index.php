<?php

use Mihaeu\Odin\Odin;
use Mihaeu\Odin\Resource\Resource;

require __DIR__.'/vendor/autoload.php';

$odin = new Odin();
$config = $odin['config'];

$userResourcePath = $odin['base.dir'].'/'.$config->get('resource_folder');
$themeResourcePath = $odin['base.dir'].'/'.$config->get('theme_folder').'/'.$config->get('theme_resource_folder');
$systemResourcePath = $odin['base.dir'].'/'.$config->get('system_resource_folder');

$userResources = $odin['locator']->locate($userResourcePath, Resource::TYPE_USER);
$themeResources = $odin['locator']->locate($themeResourcePath, Resource::TYPE_THEME);
$systemResources = $odin['locator']->locate($systemResourcePath, Resource::TYPE_SYSTEM);

$resources = array_merge($userResources, $themeResources, $systemResources);

$parsedResources = $odin['parser']->parseAll($resources);
var_dump($parsedResources[0]);
// $transformedResources = $odin['transformer']->transformAll($parsedResources);
// $odin['writer']->writeAll($transformedResources);
