<?php

namespace Mihaeu\Odin\Configuration;

/**
 * Class Configuration
 *
 * @package Mihaeu\Odin\Configuration
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $config;

    /**
     * Constructor.
     */
    public function __construct(ConfigurationFactory $configFactory)
    {
        $this->config = $configFactory->getConfiguration();
        $this->parseConfiguration();
        $this->validate();
    }

    public function parseConfiguration()
    {
        // round #1 collect all references to other items e.g. %base_dir%
        $replacements = [];
        $referencingItems = [];
        foreach ($this->getAll() as $key => $item) {
            $matches = [];
            if (!is_array($item) && preg_match_all('/%([a-zA-Z_\-]+)%+/', $item, $matches)) {
                foreach ($matches[1] as $match) {
                    if (empty($replacements["%$match%"])) {
                        $replacements["%$match%"] = $this->get($match);
                    }
                }
                $referencingItems[$key] = $item;
            }
        }

        // round #2 dereference all keys, this way the order of the config items is not important
        foreach ($referencingItems as $key => $item) {
            $this->set($key, $this->dereferenceItem($item, $replacements));
        }
    }

    public function dereferenceItem($value, $replacements)
    {
        $newValue = str_replace(array_keys($replacements), $replacements, $value);
        if (preg_match('/%([a-zA-Z_\-]+)%/', $newValue)) {
            return $this->dereferenceItem($newValue, $replacements);
        } else {
            return $newValue;
        }
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->config->get($key);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->config->set($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->config->getAll();
    }

    /**
     * Validates and possibly corrects configuration items.
     *
     * @return void
     */
    public function validate()
    {
        $this->validateFolderExistsAndIsReadable('theme_folder');
        $this->validateFolderExistsAndIsReadable('themes_folder');
        $this->validateFolderExistsAndIsReadable('resource_folder');
//        $this->validateFolderExistsAndIsReadable('theme_resource_folder', 'theme_folder');
        $this->validateFolderExistsAndIsReadable('system_resource_folder');
        $this->validateFolderExistsAndIsReadable('system_templates');
//        $this->validateFolderExistsAndIsReadable('user_templates');

        $this->validateFolderExistsAndIsWritable('output_folder');
    }

    public function validateFolderExistsAndIsReadable($key, $relativeToKey = '')
    {
        $folder = $this->get($key);
        if (!empty($relativeToKey)) {
            $folder = $this->get($relativeToKey).'/'.$folder;
        }

        if (is_dir($folder) && is_readable($folder)) {
            $folder = realpath($folder);
        } elseif (is_dir($this->get('base_dir').'/'.$folder) && is_readable($this->get('base_dir').'/'.$folder)) {
            $folder = $this->get('base_dir').'/'.$folder;
        } else {
            throw new ConfigurationException('Folder '.$folder.' does not exist or is not readable.');
        }
        $this->set($key, $folder);
    }

    public function validateFolderExistsAndIsWritable($key, $relativeToKey = '')
    {
        $folder = $this->get($key);
        if (!empty($relativeToKey)) {
            $folder = $this->get($relativeToKey).'/'.$folder;
        }

        if (is_dir($folder) && is_writable($folder)) {
            $folder = realpath($folder);
        } elseif (is_dir($this->get('base_dir').'/'.$folder) && is_writable($this->get('base_dir').'/'.$folder)) {
            $folder = $this->get('base_dir').'/'.$folder;
        } else {
            throw new ConfigurationException('Folder '.$folder.' does not exist or is not writable.');
        }
        $this->set($key, $folder);
    }
}
