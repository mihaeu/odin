<?php

namespace Mihaeu\Odin\Writer;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class Writer
 *
 * @package Mihaeu\Odin\Resource
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Writer
{
    public function write(Resource $resource)
    {
        file_put_contents($resource->destination, $resource->content);
    }

    public function writeAll(Array $resources)
    {
        foreach ($resources as $transformedResource) {
            $this->write($transformedResource);
        }
    }

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
            "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i",
            "\\1",
            $string
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
        foreach (glob($dir.'/*') as $file) {
            if (is_dir($file)) {
                $this->rrmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }
}
