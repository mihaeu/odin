<?php

namespace Mihaeu\Odin\Bootstrap;

use Mihaeu\Odin\Templating\TemplatingFactory;

/**
 * Bootstrap
 *
 * Checks, validates and creates defaults.
 *
 * @package Mihaeu\Odin\Bootstrap
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Bootstrap
{
    private $templating;

    public function __construct(TemplatingFactory $templatingFactory)
    {
        $this->templating = $templatingFactory->getTemplating();
        $this->templating->registerTemplates(__DIR__);
    }

    public function checkRequirements()
    {
        $projectDir = getcwd();
        return file_exists("$projectDir/content")
            && file_exists("$projectDir/output")
            && file_exists("$projectDir/config.yml");
    }

    public function resolveRequirements()
    {
        $projectDir = getcwd();

        if (!file_exists("$projectDir/content")) {
            mkdir("$projectDir/content");
        }

        if (!file_exists("$projectDir/output")) {
            mkdir("$projectDir/output");
        }

        if (!file_exists("$projectDir/config.yml")) {
            $data = [
                'base_dir' => realpath(__DIR__.'/../../../..'),
                'project_dir' => $projectDir
            ];
            $config = $this->templating->renderTemplate('config.yml.twig', $data);
            file_put_contents("$projectDir/config.yml", $config);
        }
    }

    public function checkAndResolveRequirements()
    {
        if (!$this->checkRequirements()) {
            $this->resolveRequirements();
        }
    }
}
