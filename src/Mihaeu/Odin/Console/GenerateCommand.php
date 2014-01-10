<?php

namespace Mihaeu\Odin\Console;

use Mihaeu\Odin\Resource\Resource;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateCommand
 *
 * @package Mihaeu\Odin\Console
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class GenerateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generates output from resources.')
            ->addOption(
                'dir',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify the directory your project resides in explicitly,'.
                ' in case you are not calling the app from the project directory itself.'
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getOption('dir');
        if (!empty($dir)) {
            chdir($dir);
        }

        $odin = $this->odin;
        if (!$odin->get('bootstrap')->checkRequirements()) {
            $output->writeln(
                "<error>Your project does not meet the requirements, please run\n\n./odin new\n\nfirst.</error>"
            );
            exit(1);
        }

        $config = $odin->get('config');
        if ($input->getOption('title')) {
            $config->set('title', $input->getOption('title'));
        }
        if ($input->getOption('url')) {
            $config->set('url', $input->getOption('url'));
        }
        if ($input->getOption('config')) {
            foreach ($input->getOption('config') as $item) {
                list($key, $value) = explode(':', $item, 2);
                $config->set($key, $value);
            }
        }

        $locator = $odin->get('locator');
        $userResources = $locator->locate($config->get('resource_folder'), Resource::TYPE_USER);
        $themeResources = $locator->locate($config->get('theme_resource_folder'), Resource::TYPE_THEME);
        $systemResources = $locator->locate($config->get('system_resource_folder'), Resource::TYPE_SYSTEM);

        $container = $odin->get('container');
        $container->addResources(array_merge($userResources, $themeResources, $systemResources));

        $odin->get('parser')->process($container);
        $odin->get('transformer')->process($container);

        $container->generateCategories();
        $container->generateTags();
        $odin->get('templating')->process($container);
        
        $writer = $odin->get('writer');
        $writer->process($container);

        $output->writeln('Cleaned output directory ('.$writer->getInfo('output_cleaned').' files)');
        $output->writeln('Copied assets (~'.$writer->getInfo('assets_copied').'kb)');
        foreach ($writer->getInfo('resources_written') as $resourceInfo) {
            $output->writeln(
                'Wrote <info>'.$resourceInfo['title'].'</info> to <comment>'.$resourceInfo['destination'].'</comment>.'
            );
        }
    }
}
