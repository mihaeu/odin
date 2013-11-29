<?php

namespace Mihaeu;

use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Odin
 *
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Odin
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
        $this->config = YAML::parse('config.yml');
        $this->meta = [
            'site'          => $this->config,
            'resources'     => []
        ];

        // find theme folders
        $themeFolder = realpath($this->config['theme_folder'].'/'.$this->config['theme']);
        $themeFolders = [];
        foreach (scandir($themeFolder) as $folder)
        {
            if (strpos($folder, '.') !== 0 && is_dir("$themeFolder/$folder"))
            {
                $themeFolders[] = "$themeFolder/$folder";
            }
        }
        if (empty($themeFolders))
        {
            $themeFolders = [$themeFolder];
        }

        $loader = new \Twig_Loader_Filesystem($themeFolders, ['debug' => true, 'autoescape' => false]);
        $this->twig = new \Twig_Environment($loader);
    }

    /**
     * @return array
     */
    public function findResources()
    {
        $path = $this->config['resource_folder'];
        $files = iterator_to_array(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path)
            )
        );
        return $files;
    }

    /**
     * @param array $files
     */
    public function parse(Array $files)
    {
        foreach ($files as $file)
        {
            if (is_file($file))
            {
                $data = file_get_contents($file);
                $token = explode('---', $data);

                if (count($token) === 3)
                {
                    $properties = YAML::parse(trim($token[1]));
                    $content = $token[2];
                }
                else if (count($token) <= 2)
                {
                    $properties = ['title' => time()];
                    $content = $data;
                }
                else
                {
                    $properties = YAML::parse(trim($token[1]));
                    $content = implode(array_splice($token, 2));
                }

                if (preg_match('/\.(md)|(markdown)$/', $file))
                {
                    $markdownParser = new MarkdownExtraParser();
                    $content = $markdownParser->transformMarkdown($content);
                }
                else if (preg_match('/\.(twig)/', $file))
                {
                    $loader = new \Twig_Loader_Filesystem($this->config['resource_folder']);
                    $twig = new \Twig_Environment($loader);

                    $resource = array_merge(['content' => $content], $properties);
                    $this->meta['resources'][$properties['slug']] = $resource;

                    continue;
                }

                if ( ! isset($properties['slug']))
                {
                    $properties['slug'] = rand(0, 9999).time().rand(0, 9999);
                }
                $resource = array_merge(['content' => $content], $properties);
                $this->meta['resources'][$properties['slug']] = $resource;

            }
        }

//        var_dump($this->meta);
//        print YAML::dump($this->meta);
    }

    /**
     * Build!
     */
    public function build()
    {
        $this->rrmdir($this->config['output_folder']);
        mkdir($this->config['output_folder']);

        foreach ($this->meta['resources'] as $slug => $resource)
        {
            if (is_numeric($slug))
            {
                $slug = $this->createSlug($resource);
            }

            if ( ! isset($resource['layout']))
            {
                $resource['layout'] = $this->config['default_layout'];
            }

            // if an extension is specified the user probably doesn't want a folder structure setup
            if ($this->config['pretty_urls'])
            {
                $file = $this->config['output_folder'].'/'.$slug.'/index.html';
            }
            else
            {
                $ext = preg_match('/\.\w+$/', $slug) ? preg_replace('/.*(\.\w+)$/', '$1', $slug) : '.html';
                $file = $this->config['output_folder'].'/'.$slug.$ext;
            }

            $renderedContent = $this->twig->render($resource['layout'], array_merge($this->meta, $resource));
            if ( ! file_exists(dirname($file)))
            {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, $renderedContent);
        }
    }

    /**
     * Create a slug using the pattern from the configuration
     *
     * @todo pattern! take date from file modification if not set
     * @param string $title
     * @return string
     */
    public function createSlug($resource)
    {
        $pattern = $this->config['permalink_pattern'];
        $tokens = preg_split('/\//', $pattern, -1, PREG_SPLIT_NO_EMPTY);
        $slugTokens = [];
        $slugMatches = [
            ':title'    => $this->sluggify($resource['title']),
            ':Y'        => date('Y', $resource['date']),
            ':y'        => date('y', $resource['date']),
            ':m'        => date('m', $resource['date']),
            ':d'        => date('d', $resource['date'])
        ];
        foreach ($tokens as $token)
        {
            $slugTokens[] = isset($slugMatches[$token]) ? $slugMatches[$token] : $token;
        }

        return implode('/', $slugTokens);
    }

    /**
     * Create a slug for nicer URLs.
     *
     * @see http://htmlblog.net/seo-friendly-url-in-php/
     *
     * @param $string
     * @return string
     */
    public function sluggify($string)
    {
        $string = preg_replace("`\[.*\]`U","",$string);
        $string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
        $string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);

        return strtolower(trim($string, '-'));
    }

    /**
     * Recursively delete everything inside a folder including the folder.
     *
     * @param $dir
     */
    public function rrmdir($dir)
    {
        foreach(glob($dir . '/*') as $file)
        {
            if(is_dir($file))
            {
                $this->rrmdir($file);
            }
            else
            {
                unlink($file);
            }
        }
        rmdir($dir);
    }
}
