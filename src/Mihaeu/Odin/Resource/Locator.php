<?php

namespace Mihaeu\Odin\Resource;

class Locator
{
	public $allowedExtensions = ['md', 'markdown', 'twig', 'html', 'xhtml', 'txt', 'xml'];

	/**
	 * @param $path
	 *
	 * @return array
	 */
	public function locate($path)
	{
		if ( ! file_exists($path) || ! is_dir($path))
		{
			return [];
		}

		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($path)
		);

		$resources = [];
		foreach ($files as $file)
		{
			// ignore linux . and ..
			// and filter resource files
			if (strpos($file->getFilename(), '.') !== 0
				&& in_array($file->getExtension(), $this->allowedExtensions))
			{
				$resources[] = new Resource($file);
			}
		}
		return $resources;
	}
}
