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
        $defaults = $bootstrap->getDefaults();

        $dialog = $this->getHelperSet()->get('dialog');
        $output->writeln(
            "<info>Bootstrapping a new odin project: Please answer the following".
                " questions in order to get started.</info>\n\n<comment>You can always change".
                " these settings later, by editing config.yml</comment>"
        );

        $configItems = [];
        $configItems['title'] = $dialog->ask(
            $output,
            $this->getText('title', $defaults),
            $defaults['title']['value']
        );
        $configItems['subtitle'] = $dialog->ask(
            $output,
            $this->getText('subtitle', $defaults),
            $defaults['subtitle']['value']
        );
        $configItems['description'] = $dialog->ask(
            $output,
            $this->getText('description', $defaults),
            $defaults['description']['value']
        );
        $configItems['author'] = $dialog->ask(
            $output,
            $this->getText('author', $defaults),
            $defaults['author']['value']
        );
        $configItems['url'] = $dialog->ask(
            $output,
            $this->getText('url', $defaults),
            $defaults['url']['value']
        );
        $configItems['date_format'] = $dialog->ask(
            $output,
            $this->getText('date_format', $defaults),
            $defaults['date_format']['value']
        );
        $configItems['pretty_urls'] = $dialog->askConfirmation(
            $output,
            $this->getText('pretty_urls', $defaults),
            true
        );
        $output->writeln('');

        $bootstrap->checkAndResolveRequirements($configItems);
    }

    public function getText($key, $defaults)
    {
        $description = isset($defaults[$key]['description'])
            ? '   <comment>'.$defaults[$key]['description']."</comment>\n"
            : '';
        return "\n - {$defaults[$key]['name']}\n".
            "$description".
            "   [Default: \"{$defaults[$key]['value']}\"]: ";
    }
}
