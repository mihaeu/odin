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
        $this->templating->registerTemplates(__DIR__.'/../Skeletons');
    }

    public function checkRequirements()
    {
        $projectDir = getcwd();
        return file_exists("$projectDir/resources/content")
            && file_exists("$projectDir/output")
            && file_exists("$projectDir/config.yml");
    }

    public function resolveRequirements()
    {
        $projectDir = getcwd();
        echo "Bootstrapping app ...\n";

        if (!file_exists("$projectDir/resources/content")) {
            mkdir("$projectDir/resources/content", 0777, true);
            mkdir("$projectDir/resources/assets", 0777, true);
            echo "Creating content directory ...\n";
        }

        if (!file_exists("$projectDir/output")) {
            mkdir("$projectDir/output");
            echo "Creating output directory ...\n";
        }

        if (!file_exists("$projectDir/config.yml")) {
            $data = [
                'base_dir' => realpath(__DIR__.'/../../../..'),
                'project_dir' => $projectDir
            ];
            $config = $this->templating->renderTemplate('config.yml.twig', $data);
            file_put_contents("$projectDir/config.yml", $config);
            echo "Creating config file ...\n";
        }
    }

    public function checkAndResolveRequirements()
    {
        if (!$this->checkRequirements()) {
            $this->resolveRequirements();
        }
    }
}
