<?php

namespace Mihaeu\Odin\Configuration;

/**
 * Class ConfigurationInterface
 *
 * @package Mihaeu\Odin\Configuration
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
interface ConfigurationInterface
{
    /**
     * Gets an item from the configuration.
     *
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Sets a configuration item, overriding old values.
     *
     * @param $key
     * @param $value
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Return all configuration items as an array.
     *
     * @return array
     */
    public function getAll();
}
