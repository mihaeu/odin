<?php

namespace Mihaeu\Odin;

use Mihaeu\Odin\Parser\ParserFactory;
use Mihaeu\Odin\Templating\TemplatingFactory;
use Mihaeu\Odin\Transformer\TransformerFactory;

/**
 * Class Odin
 *
 * @package Mihaeu
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Odin extends \Pimple
{
    private $twig;
    private $config;
    private $meta;

    /**
     * Constructor.
     *
     * @todo    check yaml file
     * @todo    get all theme subfolders not just first level
     */
    public function __construct()
    {
        $this['signature'] = <<<EOT
 _____     _ _
|  _  |   | (_)
| | | | __| |_ _ __
| | | |/ _` | | '_ \
\ \_/ / (_| | | | | |
 \___/ \__,_|_|_| |_|
EOT;
        $this['base.dir'] = realpath(__DIR__.'/../../..');
        $this['resource.extensions'] = ['md', 'markdown', 'twig', 'html', 'xhtml', 'rst', 'txt', 'xml'];

        $this['config'] = $this->share(
            function () {
                return new Configuration\YamlConfiguration();
            }
        );

        $this['templating'] = $this->share(
            function () {
                return new Templating\TwigEngine();
            }
        );

        $this['locator'] = $this->share(
            function ($this) {
                return new Locator\Locator($this['resource.extensions']);
            }
        );

        $this['parser'] = $this->share(
            function () {
                return new Parser\Parser(new ParserFactory());
            }
        );

        $this['transformer'] = $this->share(
            function () {
                return new Transformer\Transformer(new TransformerFactory());
            }
        );

        $this['writer'] = $this->share(
            function () {
                return new Writer\Writer($this['config']);
            }
        );

        $this['templating'] = $this->share(
            function () {
                return new Templating\Templating(new TemplatingFactory(), $this['config'], $this['container']);
            }
        );

        $this['container'] = $this->share(
            function () {
                return new Container\Container($this['config']);
            }
        );
    }
}
