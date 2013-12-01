<?php

namespace Mihaeu\Odin\Resource;

class Resource
{
	/**
	 * @var \SplFileInfo
	 */
	public $file;

	public function __construct(\SplFileInfo $file)
	{
		$this->file = $file;
	}
}
