<?php

use Mihaeu\Odin\Odin;
use Mihaeu\Odin\Resource\Resource;

require __DIR__.'/../vendor/autoload.php';

$odin = new Odin;
echo $odin->signature."\n\n";

$odin->get('bootstrap')->checkAndResolveRequirements();

$config = $odin->get('config');

$locator = $odin->get('locator');
$userResources = $locator->locate($config->get('resource_folder'), Resource::TYPE_USER);
$themeResources = $locator->locate($config->get('theme_resource_folder'), Resource::TYPE_THEME);
$systemResources = $locator->locate($config->get('system_resource_folder'), Resource::TYPE_SYSTEM);

$container = $odin->get('container');
$container->addResources(array_merge($userResources, $themeResources, $systemResources));

$odin->get('parser')->process($container);
$odin->get('transformer')->process($container);
$odin->get('templating')->process($container);
$odin->get('writer')->process($container);

//var_dump($config->getAll());
