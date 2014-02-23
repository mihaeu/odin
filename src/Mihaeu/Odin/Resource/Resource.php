<?php

namespace Mihaeu\Odin\Resource;

/**
 * A resource contains meta information and content.
 *
 * @package Mihaeu\Odin\Resource
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Resource
{
    const TYPE_USER = 1;
    const TYPE_THEME = 2;
    const TYPE_SYSTEM = 3;

    const META_TYPE_CONVENTION = 1;
    const META_TYPE_FRONTMATTER = 2;

    const CONTENT_TYPE_PLAIN = 1;
    const CONTENT_TYPE_TWIG = 2;
    const CONTENT_TYPE_MARKDOWN = 3;

    /**
     * var string
     */
    public $id;

    /**
     * @var \SplFileInfo
     */
    public $file;

    /**
     * @var int
     */
    public $type;

    /**
     * @var array
     */
    public $meta = [];

    /**
     * @var int
     */
    public $metaType;

    /**
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    public $contentType = -1;

    /**
     * Constructor
     *
     * @param \SplFileInfo $file
     * @param int          $type
     */
    public function __construct(\SplFileInfo $file, $type = Resource::TYPE_USER)
    {
        $this->file = $file;
        $this->type = $type;
        $this->content = file_get_contents($this->file->getRealPath());
    }

    /**
     * Generates (lazy) a unique id based on file contents and path.
     *
     * @return string
     */
    public function getId()
    {
        if (empty($this->id)) {
            $this->id = md5($this->file->getRealPath()).md5_file($this->file->getRealPath());
        }
        return $this->id;
    }
}
