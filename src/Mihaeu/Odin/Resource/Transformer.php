<?php

namespace Mihaeu\Odin\Resource;

use Mihaeu\Odin\Resource\Content\PlainTransformer;
use Mihaeu\Odin\Resource\Content\TwigTransformer;
use Mihaeu\Odin\Resource\Content\MarkdownTransformer;
use Mihaeu\Odin\Resource\Content\ContentTransformerInterface;
use dflydev\markdown\MarkdownExtraParser;

class Transformer
{
	/**
	 * @param array $resources
	 *
	 * @return array
	 */
	public function transformAll(Array $resources)
	{
		$transformedResources = [];
		foreach ($resources as $resource)
		{
			$transformedResources[] = $this->transform($resource);
		}
		return $transformedResources;
	}

	public function transform(Resource $resource)
	{
		$contentTransformer = $this->getContentTransformer($resource);
		$resource->content = $contentTransformer->transform($resource);
		return $resource;
	}

	/**
	 * @param Resource $resource
	 *
	 * @return ContentTransformerInterface
	 */
	public function getContentTransformer(Resource $resource)
	{
		if (MarkdownTransformer::isMarkdown($resource))
		{
			return new Content\MarkdownTransformer();
		}
		else if (TwigTransformer::isTwig($resource))
		{
			return new Content\TwigTransformer();
		}
		else
		{
			return new Content\PlainTransformer();
		}
	}

	/**
	 * Build!
	 */
//	public function build()
//	{
//		$this->rrmdir($this->config['output_folder']);
//		mkdir($this->config['output_folder']);
//
//		foreach ($this->meta['resources'] as $slug => $resource) {
//			if (is_numeric($slug)) {
//				$slug = $this->createSlug($resource);
//			}
//
//			if (!isset($resource['layout'])) {
//				$resource['layout'] = $this->config['default_layout'];
//			}
//
//			// if an extension is specified the user probably doesn't want a folder structure setup
//			if ($this->config['pretty_urls']) {
//				$file = $this->config['output_folder'] . '/' . $slug . '/index.html';
//			} else {
//				$ext = preg_match('/\.\w+$/', $slug) ? preg_replace('/.*(\.\w+)$/', '$1', $slug) : '.html';
//				$file = $this->config['output_folder'] . '/' . $slug . $ext;
//			}
//
//			$renderedContent = $this->twig->render($resource['layout'], array_merge($this->meta, $resource));
//			if (!file_exists(dirname($file))) {
//				mkdir(dirname($file), 0777, true);
//			}
//			file_put_contents($file, $renderedContent);
//		}
//	}

	/**
	 * Create a slug using the pattern from the configuration
	 *
	 * @todo pattern! take date from file modification if not set
	 *
	 * @param string $title
	 *
	 * @return string
	 */
//	public function createSlug($resource)
//	{
//		$pattern = $this->config['permalink_pattern'];
//		$tokens = preg_split('/\//', $pattern, -1, PREG_SPLIT_NO_EMPTY);
//		$slugTokens = [];
//		$slugMatches = [
//			':title' => $this->sluggify($resource['title']),
//			':Y'     => date('Y', $resource['date']),
//			':y'     => date('y', $resource['date']),
//			':m'     => date('m', $resource['date']),
//			':d'     => date('d', $resource['date'])
//		];
//		foreach ($tokens as $token) {
//			$slugTokens[] = isset($slugMatches[$token]) ? $slugMatches[$token] : $token;
//		}
//
//		return implode('/', $slugTokens);
//	}

	/**
	 * Create a slug for nicer URLs.
	 *
	 * @see http://htmlblog.net/seo-friendly-url-in-php/
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function sluggify($string)
	{
		$string = preg_replace("`\[.*\]`U", "", $string);
		$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $string);
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');
		$string = preg_replace(
			"`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $string
		);
		$string = preg_replace(array("`[^a-z0-9]`i", "`[-]+`"), "-", $string);

		return strtolower(trim($string, '-'));
	}

	/**
	 * Recursively delete everything inside a folder including the folder.
	 *
	 * @param $dir
	 */
	public function rrmdir($dir)
	{
		foreach (glob($dir . '/*') as $file) {
			if (is_dir($file)) {
				$this->rrmdir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dir);
	}
}
