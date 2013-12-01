<?php

namespace Mihaeu\Odin\Templating;

class TwigEngine implements Engine
{
	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \Twig_Loader_Filesystem
	 */
	private $loader;

	/**
	 * @param array $options
	 */
	public function __construct(Array $options = [])
	{
		// find theme folders
//		$themeFolder = realpath($this->config['theme_folder'] . '/' . $this->config['theme']);
//		$themeFolders = [];
//		foreach (scandir($themeFolder) as $folder) {
//			if (strpos($folder, '.') !== 0 && is_dir("$themeFolder/$folder")) {
//				$themeFolders[] = "$themeFolder/$folder";
//			}
//		}
//		if (empty($themeFolders)) {
//			$themeFolders = [$themeFolder];
//		}
//		$loader = new \Twig_Loader_Filesystem($themeFolders, ['debug' => true, 'autoescape' => false]);

		$this->loader = new \Twig_Loader_Filesystem($options);
		$this->twig = new \Twig_Environment($this->loader);
	}

	public function renderTemplate($template, $data, $options)
	{
		return $this->twig->render($template, $data);
	}

	/**
	 * @param $templateDirectory
	 */
	public function registerUserTemplates($templateDirectory)
	{
		$this->loader->addPath($templateDirectory);
	}

	/**
	 * @param $templateDirectory
	 */
	public function registerThemeTemplates($templateDirectory)
	{
		$this->loader->addPath($templateDirectory, 'theme');
	}

	/**
	 * @param $templateDirectory
	 */
	public function registerSystemTemplates($templateDirectory)
	{
		$this->loader->addPath($templateDirectory, 'system');
	}
}
