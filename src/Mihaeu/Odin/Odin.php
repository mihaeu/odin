<?php

namespace Mihaeu\Odin;

use Aura\Di\Container;
use Aura\Di\Forge;
use Aura\Di\Config;

class Odin
{
    /**
     * @var Aura\Di\Container
     */
    private $di;

    public function __construct()
    {
        $this->setupDI();
    }

    public function get($key)
    {
        return $this->di->get($key);
    }

    public function setupDI()
    {
        $di = new Container(new Forge(new Config));

        $di->params['Mihaeu\Odin\Configuration\Configuration'] = [
            'configFactory' => $di->lazyGet('configFactory')
        ];
        $di->params['Mihaeu\Odin\Locator\Locator'] = [
            'config' => $di->lazyGet('config')
        ];
        $di->params['Mihaeu\Odin\Container\Container'] = [
            'config' => $di->lazyGet('config')
        ];
        $di->params['Mihaeu\Odin\Parser\Parser'] = [
            'config' => $di->lazyGet('config'),
            'parserFactory' => $di->lazyGet('parserFactory')
        ];
        $di->params['Mihaeu\Odin\Transformer\Transformer'] = [
            'transformerFactory' => $di->lazyGet('transformerFactory'),
        ];
        $di->params['Mihaeu\Odin\Templating\Templating'] = [
            'config' => $di->lazyGet('config'),
            'templatingFactory' => $di->lazyGet('templatingFactory')
        ];
        $di->params['Mihaeu\Odin\Writer\Writer'] = [
            'config' => $di->lazyGet('config')
        ];
        $di->params['Mihaeu\Odin\Bootstrap\Bootstrap'] = [
            'templatingFactory' => $di->lazyGet('templatingFactory')
        ];

        $di->set('config', $di->lazyNew('Mihaeu\Odin\Configuration\Configuration'));
        $di->set('locator', $di->lazyNew('Mihaeu\Odin\Locator\Locator'));
        $di->set('container', $di->lazyNew('Mihaeu\Odin\Container\Container'));
        $di->set('parser', $di->lazyNew('Mihaeu\Odin\Parser\Parser'));
        $di->set('transformer', $di->lazyNew('Mihaeu\Odin\Transformer\Transformer'));
        $di->set('templating', $di->lazyNew('Mihaeu\Odin\Templating\Templating'));
        $di->set('writer', $di->lazyNew('Mihaeu\Odin\Writer\Writer'));
        $di->set('bootstrap', $di->lazyNew('Mihaeu\Odin\Bootstrap\Bootstrap'));

        $di->set('configFactory', $di->lazyNew('Mihaeu\Odin\Configuration\ConfigurationFactory'));
        $di->set('parserFactory', $di->lazyNew('Mihaeu\Odin\Parser\ParserFactory'));
        $di->set('transformerFactory', $di->lazyNew('Mihaeu\Odin\Transformer\TransformerFactory'));
        $di->set('templatingFactory', $di->lazyNew('Mihaeu\Odin\Templating\TemplatingFactory'));

        $this->di = $di;
    }
}
