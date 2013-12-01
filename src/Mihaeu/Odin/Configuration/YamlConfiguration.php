<?php

namespace Mihaeu\Odin\Configuration;

use Symfony\Component\Yaml\Yaml;

class YamlConfiguration implements Configuration
{
	/**
	 * @var array
	 */
	private $config;

	/**
	 * @todo Check if YAML throws an exception and log.
	 */
	public function __construct()
	{
		$this->config = YAML::parse('config.yml');
	}

	public function get($key)
	{
		return isset($this->config[$key])
			? $this->config[$key]
			: null;
	}

	public function set($key, $value)
	{
		$this->config[$key] = $value;
	}
}
