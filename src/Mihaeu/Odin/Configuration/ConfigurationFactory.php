<?php

namespace Mihaeu\Odin\Configuration;

/**
 * Class ConfigurationFactory
 *
 * @package Mihaeu\Odin\Configuration
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class ConfigurationFactory
{
    /**
     * Produces the appropriate Configuration class.
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return new YamlConfiguration();
    }
}
