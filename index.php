<?php

use Mihaeu\Odin\Odin;
use Mihaeu\Odin\Resource\Resource;

require __DIR__.'/vendor/autoload.php';

$odin = new Odin;

$config = $odin->get('config');
$config->set('base_dir', __DIR__);

$userResourcePath = __DIR__.'/'.$config->get('resource_folder');
$themeResourcePath = __DIR__.'/'.$config->get('theme_folder').'/'.$config->get('theme_resource_folder');
$systemResourcePath = __DIR__.'/'.$config->get('system_resource_folder');

$locator = $odin->get('locator');
$userResources = $locator->locate($userResourcePath, Resource::TYPE_USER);
$themeResources = $locator->locate($themeResourcePath, Resource::TYPE_THEME);
$systemResources = $locator->locate($systemResourcePath, Resource::TYPE_SYSTEM);

$container = $odin->get('container');
$container->addResources(array_merge($userResources, $themeResources, $systemResources));

$odin->get('parser')->parseContainer($container);
$odin->get('transformer')->transformContainer($container);
$odin->get('templating')->renderContainer($container);
$odin->get('writer')->writeContainer($container);
