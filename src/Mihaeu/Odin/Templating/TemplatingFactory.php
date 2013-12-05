<?php

namespace Mihaeu\Odin\Templating;

use Mihaeu\Odin\Configuration\ConfigurationInterface;

/**
 * Class TemplatingFactory
 * @package Mihaeu\Odin\Templating
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class TemplatingFactory
{
    public function getTemplating()
    {
        return new TwigTemplating(['autoescape' => false]);
    }
}
