<?php

namespace Mihaeu\Odin\Templating;

class TwigTemplating implements TemplatingInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Twig_Loader_Filesystem
     */
    private $loader;

    private $stringTwig;

    /**
     * @param array $options
     */
    public function __construct(Array $options = [])
    {
        $this->loader = new \Twig_Loader_Filesystem();
        $this->twig = new \Twig_Environment($this->loader, $options);
        $this->twig->addExtension(new \Twig_Extension_Debug());

        $stringLoader = new \Twig_Loader_String();
        $this->stringTwig = new \Twig_Environment($stringLoader, $options);
    }

    public function renderTemplate($template, $data, $options = [])
    {
        return $this->twig->render($template, $data);
    }

    public function renderString($string, $data, $options = [])
    {
        return $this->stringTwig->render($string, $data);
    }

    /**
     * @param $templateDirectory
     * @param string $type
     */
    public function registerTemplates($templateDirectory, $type = '')
    {
        $folders = $this->findAllSubfolders($templateDirectory);
        if (!empty($folders)) {
            if ($type !== '') {
                $this->loader->addPath($templateDirectory, $type);
            } else {
                $this->loader->addPath($templateDirectory);
            }
        }
    }

    private function findAllSubfolders($path)
    {
        if (!is_dir($path) && !is_readable($path)) {
            return [];
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveIteratorIterator::SELF_FIRST)
        );

        $subfolders = [$path];
        foreach (iterator_to_array($files, true) as $realPath => $file) {
            // exclude unix . and config files like .git and take only readable directories
            if (strpos($file->getFilename(), '.') !== 0 && is_dir($file) && is_readable($file)) {
                $subfolders[] = $realPath;
            }
        }
        return $subfolders;
    }
}
