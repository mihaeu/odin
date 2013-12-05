<?php

namespace Mihaeu\Odin\Templating;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Configuration\ConfigurationInterface;
use Mihaeu\Odin\Container\Container;

/**
 * Class Templating
 * @package Mihaeu\Odin\Templating
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Templating
{
    /**
     * @var TemplatingInterface
     */
    private $templating;

    /**
     * @var ConfigurationInterface
     */
    private $cfg;

    /**
     * @var Container
     */
    private $container;

    /**
     * Constructor.
     */
    public function __construct(TemplatingFactory $templatingFactory, ConfigurationInterface $cfg, Container $container)
    {
        $this->cfg = $cfg;
        $this->container = $container;
        $this->templating = $templatingFactory->getTemplating();

        $userTemplates = $cfg->get('base_dir').'/'.$this->cfg->get('user_templates');
        $this->templating->registerTemplates($userTemplates);

        $themeTemplates = $cfg->get('base_dir').'/'.$this->cfg->get('theme_folder').'/'.$this->cfg->get('theme');
        $this->templating->registerTemplates($themeTemplates, 'theme');

        $systemTemplates = $cfg->get('base_dir').'/'.$this->cfg->get('system_templates');
        $this->templating->registerTemplates($systemTemplates, 'system');
    }

    public function render(Resource $resource, Array $containerArray)
    {
        // standalone resources do not not have a template, they are templates themselves and
        // have already been parsed as a resource
        if (!isset($resource->meta['standalone']) || $resource->meta['standalone'] === false) {
            $template = isset($resource->meta['layout'])
                ? $resource->meta['layout']
                : $this->cfg->get('default_template');
            $template = '@theme/index.html.twig';

            $data = array_merge(
                $containerArray,
                $resource->meta,
                [
                    'content' => $resource->content,
                    'site'    => $this->cfg->getAll()
                ]
            );
            $resource->content = $this->templating->renderTemplate($template, $data);
        }
        return $resource;
    }

    public function renderAll(Array $resources)
    {
        $containerArray = $this->container->getContainerArray($resources);
        $renderedResources = [];
        foreach ($resources as $resource) {
            $renderedResources[] = $this->render($resource, $containerArray);
        }
        return $renderedResources;
    }
}
