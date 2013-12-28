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
            ->setDescription('Generates output from resources.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $odin = $this->odin;
        if (!$odin->get('bootstrap')->checkRequirements()) {
            $output->writeln(
                "<error>Your project does not meet the requirements, please run\n\n./odin new\n\nfirst.</error>"
            );
            exit(1);
        }

        $config = $odin->get('config');

        $locator = $odin->get('locator');
        $userResources = $locator->locate($config->get('resource_folder'), Resource::TYPE_USER);
        $themeResources = $locator->locate($config->get('theme_resource_folder'), Resource::TYPE_THEME);
        $systemResources = $locator->locate($config->get('system_resource_folder'), Resource::TYPE_SYSTEM);

        $container = $odin->get('container');
        $container->addResources(array_merge($userResources, $themeResources, $systemResources));

        $odin->get('parser')->process($container);
        $odin->get('transformer')->process($container);
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
