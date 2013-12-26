<?php

namespace Mihaeu\Odin\Console;

use Symfony\Component\Console\Application as BaseApplication;

/**
 * Class Application
 * @package Mihaeu\Odin\Console
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('odin');
    }

    public function getHelp()
    {
        $help = array(
            $this->signature,
            '',
            '<info>Odin</info> is the <comment>most convenient</comment> PHP static site generator.'
        );

        return implode("\n", $help);
    }

    public $signature = "\033[1;33m   ___    _ _
  /___\__| (_)_ __
 //  // _` | | '_ \
/ \_// (_| | | | | |
\___/ \__,_|_|_| |_|\033[0m";

}
