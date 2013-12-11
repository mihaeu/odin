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
        $configFile = file_exists('config.yml') ? 'config.yml' : '../config.yml';
        $this->config = YAML::parse($configFile);
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
