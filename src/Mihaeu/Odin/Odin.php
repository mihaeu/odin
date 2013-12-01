<?php

namespace Mihaeu\Odin;

/**
 * Class Odin
 *
 * @package Mihaeu
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Odin extends \Pimple
{
	private $twig;
	private $config;
	private $meta;

	/**
	 * Constructor.
	 *
	 * @todo    check yaml file
	 * @todo    get all theme subfolders not just first level
	 */
	public function __construct()
	{
		$this['signature'] = <<<EOT
 _____     _ _
|  _  |   | (_)
| | | | __| |_ _ __
| | | |/ _` | | '_ \
\ \_/ / (_| | | | | |
 \___/ \__,_|_|_| |_|
EOT;
		$this['base.dir'] = realpath(__DIR__.'/../../..');
		$this['resource.extensions'] = ['md', 'markdown', 'twig', 'html', 'xhtml', 'txt', 'xml'];

		$this['config'] = $this->share(function ()
		{
			return new Configuration\YamlConfiguration();
		});

		$this['templating'] = $this->share(function ()
		{
			return new Templating\TwigEngine();
		});

		$this['locator'] = $this->share(function ($this)
		{
			return new Resource\Locator($this['resource.extensions']);
		});

		$this['parser'] = $this->share(function ()
		{
			return new Resource\Parser();
		});

		$this['transformer'] = $this->share(function ()
		{
			return new Resource\Transformer();
		});

		$this['writer'] = $this->share(function()
		{
			return new Resource\Writer();
		});

		$this['log'] = $this->share(function()
		{
			return new \Monolog\Logger('logger');
		});
	}
}
