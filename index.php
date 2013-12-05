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

$resources = array_merge($userResources, $themeResources, $systemResources);

$parsedResources = $odin['parser']->parseAll($resources);
$transformedResources = $odin['transformer']->transformAll($parsedResources);
$renderedTemplates = $odin['templating']->renderAll($transformedResources);
//$odin['writer']->writeAll($renderedResources);

//var_dump($transformedResources);

foreach ($resources as $resource) {
    file_put_contents('/tmp/'.$resource->file->getFilename().'.html', $resource->content);
}
