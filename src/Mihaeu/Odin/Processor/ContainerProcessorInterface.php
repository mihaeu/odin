<?php

namespace Mihaeu\Odin\Processor;

use Mihaeu\Odin\Container\Container;

interface ContainerProcessorInterface
{
    public function process(Container &$container);
}
