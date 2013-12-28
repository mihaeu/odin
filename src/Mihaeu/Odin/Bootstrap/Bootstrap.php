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
                $this->getDefaults(),
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

    /**
     * @return array
     */
    public function getDefaults()
    {
        return [
            'title'             => [
                'name'  => 'Title',
                'value' => 'Example Blog'
            ],
            'subtitle'          => [
                'name'  => 'Subtitle',
                'value' => 'This is just the beginning ... example.'
            ],
            'description'       => [
                'name'  => 'Description',
                'value' => 'This blog is all about giving examples.'
            ],
            'author'            => [
                'name'        => 'Author',
                'value'       => 'William Shakespear',
                'description' => 'Authors are optional, but will be used for humans.txt,'.
                    ' copyright, footer, post meta data etc.'
            ],
            'url'               => [
                'name'        => 'URL',
                'value'       => 'http://localhost:8080',
                'description' => 'Please provide the URL to your blog without a trailing slash. '
            ],
            'date_format'       => [
                'name'        => 'Date Format',
                'value'       => 'd.m.Y',
                'description' => "Default date format throughout the blog. You can override this everywhere".
                    " in your templates by using Twigs date() function.\n   (see: http://twig.sensiolabs.org/doc/functions/date.html)"
            ],
            'permalink_pattern' => [
                'name'        => 'Permalink Pattern',
                'value'       => '/:title/',
                'description' => "options: :title :Y :y :m :d\n\nExample: /blog/:Y/:m/:title will".
                    " produce something like /blog/2014/01/awesome-post"
            ],
            'pretty_urls'       => [
                'name'        => 'Pretty URLs',
                'value'       => 'Y/n',
                'description' => "If true will create a file structure like\n\n   /awesome-post/index.html\n\n".
                    "   so the link will be: /awesome-post instead of /awesome-post.html"
            ]
        ];
    }
}
