<?php

namespace Mihaeu\Odin\Configuration;

interface ConfigurationInterface
{
    public function get($key);

    public function set($key, $value);

    public function getAll();
}
