<?php

use Mihaeu\Odin\Odin;
use Mihaeu\Odin\Resource\Resource;

require __DIR__.'/../vendor/autoload.php';

$odin = new Odin;
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
$writer = $odin->get('writer');
$writer->process($container);

printf(
    "%s\n\n%s\n%s\n\n%s\n",
    $odin->signature,
    $writer->getInfo('output_cleaned'),
    $writer->getInfo('assets_copied'),
    implode("\n", $writer->getInfo('resources_written'))
);

//var_dump($config->getAll());
