<?php

namespace Mihaeu\Odin\Templating;

interface Engine
{
	public function renderTemplate($template, $data, $options);

	public function registerUserTemplates($templateDirectory);

	public function registerThemeTemplates($templateDirectory);

	public function registerSystemTemplates($templateDirectory);
}
