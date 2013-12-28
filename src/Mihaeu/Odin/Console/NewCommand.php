<?php

namespace Mihaeu\Odin\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class NewCommand
 *
 * @package Mihaeu\Odin\Console
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class NewCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Bootstrap a new project.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $odin = $this->odin;
        $bootstrap = $odin->get('bootstrap');
        $defaults = $bootstrap->defaults;

        $dialog = $this->getHelperSet()->get('dialog');
        $output->writeln(
            "<info>Bootstrapping a new odin project: Please answer the following".
                " questions in order to get started.</info>\n\n<comment>You can always change".
                " these settings later, by editing config.yml</comment>\n"
        );

        $configItems = [];
        $configItems['title'] = $dialog->ask(
            $output,
            "Blog Title [{$defaults['title']}]: ",
            $defaults['title']
        );
        $configItems['subtitle'] = $dialog->ask(
            $output,
            "Blog Subtitle [{$defaults['subtitle']}]: ",
            $defaults['subtitle']
        );
        $configItems['description'] = $dialog->ask(
            $output,
            "Blog Description [{$defaults['description']}]: ",
            $defaults['description']
        );
        $configItems['author'] = $dialog->ask(
            $output,
            "Author [{$defaults['author']}]: ",
            $defaults['author']
        );
        $configItems['url'] = $dialog->ask(
            $output,
            "URL [{$defaults['url']}]: ",
            $defaults['url']
        );
        $configItems['date_format'] = $dialog->ask(
            $output,
            "Date Format [{$defaults['date_format']}]: ",
            $defaults['date_format']
        );
        $configItems['pretty_urls'] = $dialog->askConfirmation(
            $output,
            "Pretty URLs (/about vs. /about.html) [{$defaults['pretty_urls']}]: ",
            $defaults['pretty_urls']
        );

        $bootstrap->checkAndResolveRequirements($configItems);
    }
}
