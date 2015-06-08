<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 05/05/2015
 * Time: 23:58
 */

namespace Mykees\TagBundle\Form;


use Mykees\TagBundle\Manager\TagManager;
use Symfony\Component\Form\DataTransformerInterface;

class TagTransformer implements DataTransformerInterface
{


    public $manager;

    public function __construct(TagManager $manager)
    {
        $this->manager = $manager;
    }

    public function transform($tags)
    {
        return "";
    }

    public function reverseTransform($tags)
    {
        if ($tags) {
            $explode = explode(',', $tags);
            $filterTags = array_unique($explode);
            $arrayTags = [];
            foreach ($filterTags as $tag) {
                $arrayTags[] = $tag;
            }

            return $this->manager->manageTags($arrayTags);
        }
    }
}
