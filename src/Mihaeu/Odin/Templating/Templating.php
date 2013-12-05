<?php

namespace Mihaeu\Odin\Templating;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Configuration\ConfigurationInterface;

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
     * Constructor.
     */
    public function __construct(TemplatingFactory $templatingFactory, ConfigurationInterface $cfg)
    {
        $this->cfg = $cfg;
        $this->templating = $templatingFactory->getTemplating();

        $userTemplates = $cfg->get('base_dir').'/'.$this->cfg->get('user_templates');
        $this->templating->registerTemplates($userTemplates);

        $themeTemplates = $cfg->get('base_dir').'/'.$this->cfg->get('theme_folder').'/'.$this->cfg->get('theme');
        $this->templating->registerTemplates($themeTemplates, 'theme');

        $systemTemplates = $cfg->get('base_dir').'/'.$this->cfg->get('system_templates');
        $this->templating->registerTemplates($systemTemplates, 'system');
    }

    public function render(Resource $resource)
    {
        // standalone resources do not not have a template, they are templates themselves and
        // have already been parsed as a resource
        if (!isset($resource->meta['standalone']) || $resource->meta['standalone'] === false) {
            $template = isset($resource->meta['layout'])
                ? $resource->meta['layout']
                : $this->cfg->get('default_template');
            $template = '@theme/index.html.twig';
            $resource->content = $this->templating->renderTemplate($template, [
                    'content' => $resource->content
            ] + ['site' => $this->cfg->getAll()] + $resource->meta);
        }
        return $resource;
    }

    public function renderAll(Array $resources)
    {
        $renderedResources = [];
        foreach ($resources as $resource) {
            $renderedResources[] = $this->render($resource);
        }
        return $renderedResources;
    }
}
