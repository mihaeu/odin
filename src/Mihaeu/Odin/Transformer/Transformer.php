<?php

namespace Mihaeu\Odin\Transformer;

use Mihaeu\Odin\Resource\Resource;
use Mihaeu\Odin\Container\Container;
use Mihaeu\Odin\Processor\ContainerProcessorInterface;
use dflydev\markdown\MarkdownExtraParser;

class Transformer implements ContainerProcessorInterface
{
    /**
     * @var TransformerFactory
     */
    private $transformerFactory;

    public function __construct(TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
    }

    public function process(Container &$container)
    {
        foreach ($container->getResources() as $resource) {
            $resource = $this->transform($resource);
            $container->setResource($resource->getId(), $resource);
        }
    }

    /**
     * @param array $resources
     *
     * @return array
     */
    public function transformAll(Array $resources)
    {
        $transformedResources = [];
        foreach ($resources as $resource) {
            $transformedResources[] = $this->transform($resource);
        }
        return $transformedResources;
    }

    public function transform(Resource $resource)
    {
        $contentTransformer = $this->transformerFactory->getTransformer($resource);
        $resource->content = $contentTransformer->transform($resource);
        return $resource;
    }
}
