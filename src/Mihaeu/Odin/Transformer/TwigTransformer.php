<?php

namespace Mihaeu\Odin\Transformer;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class TwigTransformer
 * @package Mihaeu\Odin\Resource\Transformer
 * @author  Michael Haeuslmann <haeuslmann@gmail.com>
 */
class TwigTransformer implements TransformerInterface
{
    /**
     * @param Resource $resource
     *
     * @return bool
     */
    public static function isTwig(Resource $resource)
    {
        $contentTypeSet = isset($resource->meta['content_type']);
        $contentTypeSetAndCorrect = $contentTypeSet && $resource->meta['content_type'] === 'twig';

//		$markdownFileExtensions = ['twig'];
//		$correctExtension = in_array(strtolower($resource->file->getExtension()), $markdownFileExtensions);
        $correctExtension = strtolower($resource->file->getExtension()) === 'twig';

        $contentTypeNotSetButCorrectExtension = !$contentTypeSet && $correctExtension;

        return $contentTypeSetAndCorrect || $contentTypeNotSetButCorrectExtension;
    }

    public function transform(Resource &$resource)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $resource->contentType = Resource::CONTENT_TYPE_TWIG;

        // do not parse standalone files at this stage
        if (empty($resource->meta['standalone'])) {
            $content = $twig->render($resource->content);
        } else {
            $content = $resource->content;
        }
        return $content;
    }
}
