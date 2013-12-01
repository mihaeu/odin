<?php

namespace Mihaeu\Odin\Resource;

class Resource
{
	const TYPE_USER = 1;
	const TYPE_THEME = 2;
	const TYPE_SYSTEM = 3;

	/**
	 * @var \SplFileInfo
	 */
	public $file;

	/**
	 * @var int
	 */
	public $type;

	/**
	 * @var Meta\Meta
	 */
	public $meta;

	/**
	 * @var Content\Content
	 */
	public $content;

	public function __construct(\SplFileInfo $file, $type = Resource::TYPE_USER)
	{
		$this->file = $file;
		$this->type = $type;
	}

	public function isParsed()
	{
		return ! empty($this->meta);
	}

	public function isTransformed()
	{
		return ! empty($this->content);
	}
}
