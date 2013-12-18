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
        // since this project depends on composer and the composer ClassLoader is
        // always in the same directory, we can use Reflection to find its
        // location and the base directory located two levels below
//        $reflector = new \ReflectionClass('Composer\Autoload\ClassLoader');
//        $configDirectory = realpath(dirname($reflector->getFileName()).'/../..');
        return new YamlConfiguration(getcwd());
    }
}
