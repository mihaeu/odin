<?php

namespace Mihaeu\Odin\Transformer;

use Mihaeu\Odin\Resource\Resource;
use dflydev\markdown\MarkdownExtraParser;
use Ciconia\Ciconia;

/**
 * Class MarkdownTransformer
 *
 * @package Mihaeu\Odin\Resource\Transformer
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class MarkdownTransformer implements TransformerInterface
{
    /**
     * @param Resource $resource
     *
     * @return bool
     */
    public static function isMarkdown(Resource $resource)
    {
        $contentTypeSet = isset($resource->meta['content_type']);
        $contentTypeSetAndCorrect = $contentTypeSet && $resource->meta['content_type'] === 'markdown';

        $markdownFileExtensions = ['md', 'markdown'];
        $correctExtension = in_array(strtolower($resource->file->getExtension()), $markdownFileExtensions);

        $contentTypeNotSetButCorrectExtension = !$contentTypeSet && $correctExtension;

        return $contentTypeSetAndCorrect || $contentTypeNotSetButCorrectExtension;
    }

    /**
     * Transform a resource's content.
     *
     * @TODO pandoc support
     *
     * @param Resource $resource
     *
     * @return string
     */
    public function transform(Resource &$resource)
    {
        $content = '';
        $configPandoc = false;
        $pandocIsInstalled = false;
        $configGithubFLavoredMarkdown = true;
        if ($configPandoc && $pandocIsInstalled) {
            // use pandoc
            throw new \Exception("Pandoc support has not been implemented yet. Sorry.");
        } else {
            if (defined(PHP_VERSION_ID) && PHP_VERSION_ID >= 50400) {
                $ciconia = new Ciconia();
                if ($configGithubFLavoredMarkdown) {
                    $ciconia->addExtension(new Gfm\FencedCodeBlockExtension());
                    $ciconia->addExtension(new Gfm\TaskListExtension());
                    $ciconia->addExtension(new Gfm\InlineStyleExtension());
                    $ciconia->addExtension(new Gfm\WhiteSpaceExtension());
                    $ciconia->addExtension(new Gfm\TableExtension());
                    $ciconia->addExtension(new Gfm\UrlAutoLinkExtension());
                }
                $content = $ciconia->render($resource->content);
            } else {
                $markdownParser = new MarkdownExtraParser();
                $content = $markdownParser->transformMarkdown($resource->content);
            }
        }

        $resource->contentType = Resource::CONTENT_TYPE_MARKDOWN;
        return $content;
    }
}
