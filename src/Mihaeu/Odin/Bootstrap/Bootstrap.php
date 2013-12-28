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
    /**
     * @var \Mihaeu\Odin\Templating\TwigTemplating
     */
    private $templating;

    /**
     * @var array
     */
    public $defaults = [
//        'title'             => [
//            'value'       => 'Example Blog',
//            'description' => ''
//        ],
        'title'             => 'Example Blog',
        'subtitle'          => 'This is just the beginning ... example.',
        'description'       => 'This blog is all about giving examples.',
        'author'            => 'William Shakespear',
        'url'               => 'http://localhost:8080',
        'date_format'       => 'd.m.Y',
        'permalink_pattern' => '/:title/',
        'pretty_urls'       => 'true'
    ];

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

    public function resolveRequirements(Array $configItems = null)
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
            $data = array_merge(
                $this->defaults,
                [
                    'base_dir'    => realpath(__DIR__.'/../../../..'),
                    'project_dir' => $projectDir
                ]
            );
            if ($configItems !== null) {
                // specified config overwrites defaults
                $data = array_merge($data, $configItems);
            }
            $config = $this->templating->renderTemplate('config.yml.twig', $data);
            file_put_contents("$projectDir/config.yml", $config);
            echo "Creating config file ...\n";
        }
    }

    public function checkAndResolveRequirements(Array $configItems = null)
    {
        if (!$this->checkRequirements()) {
            $this->resolveRequirements($configItems);
        }
    }
}
