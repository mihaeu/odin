<?php

namespace Mihaeu\Odin\Console;

use Mihaeu\Odin\Odin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseCommand
 *
 * @package Mihaeu\Odin\Console
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class BaseCommand extends Command
{
    /**
     * @var Odin
     */
    protected $odin;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->odin = new Odin;
    }

    /**
     * @inheritdoc
     */
    public function initialize(InputInterface $input = null, OutputInterface $output = null)
    {
        $output->writeln($this->getApplication()->signature."\n");
    }

    /**
     * Configures the command before it is executed.
     * 
     * Sets arguments and options that are common for all commands.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->addOption(
                'url',
                null,
                InputOption::VALUE_REQUIRED,
                'Sets or overwrites the site url configuration value.'
            )
            ->addOption(
                'title',
                null,
                InputOption::VALUE_REQUIRED,
                'Sets or overwrites the site title configuration value.'
            )->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets or overwrites a key:value config pair (e.g. --config="subtitle:Check this out!").'
            );
    }
}
