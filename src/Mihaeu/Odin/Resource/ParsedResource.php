<?php

namespace Mihaeu\Odin\Resource;

/**
 * Class ParsedResource
 *
 * @package Mihaeu\Odin\Resource
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class ParsedResource
{
	/**
	 * @var array
	 */
	public $meta;

	/**
	 * @var string
	 */
	public $content;

	public function __construct(array $meta = [], $content = '')
	{
		$this->$meta = $meta;
		$this->content = $content;
	}
}
