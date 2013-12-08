<?php

namespace Mihaeu\Odin\Configuration;

/**
 * Class Configuration
 *
 * @package Mihaeu\Odin\Configuration
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * Constructor.
     */
    public function __construct(ConfigurationFactory $configFactory)
    {
        $this->config = $configFactory->getConfiguration();
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->config->get($key);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->config->set($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->config->getAll();
    }
}
