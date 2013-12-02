<?php

namespace Mihaeu\Odin\Resource\Content;

use Mihaeu\Odin\Resource\Resource;

/**
 * Class TwigTransformer
 * @package Mihaeu\Odin\Resource\Content
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class TwigTransformer implements ContentTransformerInterface
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

		$contentTypeNotSetButCorrectExtension = ! $contentTypeSet && $correctExtension;

		return $contentTypeSetAndCorrect || $contentTypeNotSetButCorrectExtension;
	}

    public function transform(Resource &$resource)
    {
	    $twig = new \Twig_Environment(new \Twig_Loader_String());

	    $resource->contentType = Resource::CONTENT_TYPE_TWIG;
	    return $twig->render($resource->content);
    }
}
