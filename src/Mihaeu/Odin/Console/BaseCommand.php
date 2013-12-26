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

    public function __construct()
    {
        parent::__construct();

        $this->odin = new Odin;
    }

    protected function initialize(InputInterface $input = null, OutputInterface $output = null)
    {
        $output->writeln($this->getApplication()->signature."\n");
    }
}
