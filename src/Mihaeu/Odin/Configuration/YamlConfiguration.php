<?php

namespace Mihaeu\Odin\Configuration;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlConfiguration
 *
 * @package Mihaeu\Odin\Configuration
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class YamlConfiguration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * Constructor parses the YAML configuration.
     *
     * @todo Check if YAML throws an exception and log.
     */
    public function __construct()
    {
//        $configFile = file_exists('config.yml') ? 'config.yml' : '../config.yml';

        // since this project depends on composer and the composer ClassLoader is
        // always in the same directory, we can use Reflection to find its
        // location and the base directory located two levels below
        $reflector = new \ReflectionClass('Composer\Autoload\ClassLoader');
        $configDirectory = realpath(dirname($reflector->getFileName()).'/../..');
        $this->config = YAML::parse($configDirectory.'/config.yml');
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        if (isset($this->config[$key]))
        {
            return $this->config[$key];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->config;
    }
}
