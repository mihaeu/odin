<?php

namespace Mihaeu\Odin\Templating;

interface TemplatingInterface
{
    public function renderTemplate($template, $data, $options = []);

    public function registerTemplates($templateDirectory, $type);
}
