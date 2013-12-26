<?php

namespace Mihaeu\Odin\Console;

/**
 * Class NewCommand
 *
 * @package Mihaeu\Odin\Console
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
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
        $odin->get('bootstrap')->checkAndResolveRequirements();
    }
}
