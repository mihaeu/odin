<?php

use Mihaeu\Odin\Odin;
use Mihaeu\Odin\Resource\Resource;

require __DIR__.'/vendor/autoload.php';

$odin = new Odin();
$cfg = $odin['config'];
$cfg->set('base_dir', $odin['base.dir']);

$userResourcePath = $odin['base.dir'].'/'.$cfg->get('resource_folder');
$themeResourcePath = $odin['base.dir'].'/'.$cfg->get('theme_folder').'/'.$cfg->get('theme_resource_folder');
$systemResourcePath = $odin['base.dir'].'/'.$cfg->get('system_resource_folder');

$locator = $odin['locator'];
$userResources = $locator->locate($userResourcePath, Resource::TYPE_USER);
$themeResources = $locator->locate($themeResourcePath, Resource::TYPE_THEME);
$systemResources = $locator->locate($systemResourcePath, Resource::TYPE_SYSTEM);

$container = $odin['container'];
$container->addResources(array_merge($userResources, $themeResources, $systemResources));

$odin['parser']->parseContainer($container);
$odin['transformer']->transformContainer($container);

$odin['templating']->renderContainer($container);
$odin['writer']->writeContainer($container);

var_dump($container->getContainerArray());exit;
