<?php

use Mihaeu\Odin\Odin;
use Mihaeu\Odin\Resource\Resource;

require __DIR__.'/../vendor/autoload.php';

$odin = new Odin;
echo $odin->signature."\n\n";

$config = $odin->get('config');
$config->set('base_dir', realpath(__DIR__.'/..'));
$config->validate();

$locator = $odin->get('locator');
$userResources = $locator->locate($config->get('resource_folder'), Resource::TYPE_USER);
$themeResources = $locator->locate($config->get('theme_folder').'/'.$config->get('theme'), Resource::TYPE_THEME);
$systemResources = $locator->locate($config->get('system_resource_folder'), Resource::TYPE_SYSTEM);

$container = $odin->get('container');
$container->addResources(array_merge($userResources, $themeResources, $systemResources));

$odin->get('parser')->parseContainer($container);
$odin->get('transformer')->transformContainer($container);
$odin->get('templating')->renderContainer($container);
$odin->get('writer')->writeContainer($container);

//var_dump($config->getAll());
