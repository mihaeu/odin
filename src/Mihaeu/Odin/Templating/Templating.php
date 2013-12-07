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

    public function renderContainer(Container &$container)
    {
        $containerArray = $container->getContainerArray();
        foreach ($container->getResources() as $resource) {
            // standalone resources do not not have a template, they are templates themselves and
            // have already been parsed as a resource
            if (empty($resource->meta['standalone'])) {
                $template = $this->cfg->get('default_layout');
                if (!empty($resource->meta['layout'])) {
                    $template = $resource->meta['layout'];
                }

                // render template using container and current resource
                $resource->content = $this->templating->renderTemplate(
                    $template,
                    array_merge(
                        $containerArray,
                        $resource->meta,
                        [
                            'content' => $resource->content,
                        ]
                    )
                );
            }
            $container->setResource($resource->getId(), $resource);
        }
    }
}
