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
    private $config;

    /**
     * Constructor.
     */
    public function __construct(TemplatingFactory $templatingFactory, ConfigurationInterface $config)
    {
        $this->config = $config;
        $this->templating = $templatingFactory->getTemplating();

        $userTemplates = $this->config->get('user_templates');
        $this->templating->registerTemplates($userTemplates);

        $themeTemplates = $this->config->get('theme');
        $this->templating->registerTemplates($themeTemplates, 'theme');

        $systemTemplates = $this->config->get('system_templates');
        $this->templating->registerTemplates($systemTemplates, 'system');
    }

    public function renderContainer(Container &$container)
    {
        $containerArray = $container->getContainerArray();
        foreach ($container->getResources() as $resource) {
            // standalone resources do not not have a template, they are templates themselves and
            // have already been parsed as a resource
            if (empty($resource->meta['standalone'])) {
                $template = $this->config->get('default_layout');
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
            } else {
                $resource->content = $this->templating->renderString(
                    $resource->content,
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
