<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 11/12/2014
 * Time: 15:47
 */

namespace Mykees\TagBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mykees\TagBundle\Entity\TagRelation;
use Mykees\TagBundle\Entity\Tag;

class LoadTagRelation implements FixtureInterface {

    function load(ObjectManager $manager)
    {
        for($i=1; $i<15; $i++)
        {
            $tag = new Tag();
            $tag->setName('Tag'.$i);
            $tag->setSlug('tag'.$i);
            $manager->persist($tag);
            $manager->flush();

            $tagRelation = new TagRelation();
            $tagRelation->setModel('Post');
            $tagRelation->setModelId($i);
            $tagRelation->setTag($tag);
            $manager->persist($tagRelation);
            $manager->flush();
        }
    }
}