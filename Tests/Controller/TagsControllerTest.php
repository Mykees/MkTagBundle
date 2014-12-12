<?php

namespace Mykees\TagBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class TagsControllerTest extends WebTestCase
{
    protected $client;
    protected $container;
    protected $manager;
    protected $em;

    public function setUp()
    {
        //Load fixtures
        $fixtures = [
            'Mykees\BlogBundle\DataFixtures\ORM\LoadPosts',
            'Mykees\TagBundle\DataFixtures\ORM\LoadTagRelation',
        ];
        $this->loadFixtures($fixtures);

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->manager = $this->container->get('mk.tag.manager');
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        parent::setUp();
    }

    /*==============*\
        = Tag
    \*==============*/
    public function testCreateAValidTag(){
        $this->manager->createTag('jambon fumé');
        $count = $this->manager->countTags();
        $this->assertEquals(15, $count);
    }

    public function testCreateValidArrayTags(){
        $leagueOfLegends = ['Teemo','Corki','Garen','Yi','Akali','Irelia','Twitch'];
        $this->manager->manageTags($leagueOfLegends);
        $count = $this->manager->countTags();
        $this->assertEquals(21, $count);
    }

    public function testAddExistedTags(){
        $existed = ['tag1','tag2','tag3'];
        $this->manager->manageTags($existed);
        $count = $this->manager->countTags();
        $this->assertEquals(14, $count);
    }

    public function testNoValidTags(){
        $noValid = ['         ','         '];
        $this->manager->manageTags($noValid);
        $count = $this->manager->countTags();
        $this->assertEquals(14, $count);
    }

    /*==============*\
      = TagRelation
    \*==============*/

    public function testAddValidRelation(){
        $post = $this->em->getRepository('MykeesBlogBundle:Post')->find(2);
        $post->getTags()->set('name','XboxOne,Ps4,Wii U');
        $this->manager->saveRelation($post);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(17, $count);
    }

    public function testAddExistedRelation(){
        $post = $this->em->getRepository('MykeesBlogBundle:Post')->find(1);
        $post->getTags()->set('name','Tag1,Ps4,Wii U');
        $this->manager->saveRelation($post);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(16, $count);
    }

    public function testRemoveRelation(){
        $post = $this->em->getRepository('MykeesBlogBundle:Post')->find(1);
        $this->manager->deleteTagRelation($post);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(13, $count);
    }

    public function testRemoveUselessTag(){
        $post1 = $this->em->getRepository('MykeesBlogBundle:Post')->find(1);
        $post2 = $this->em->getRepository('MykeesBlogBundle:Post')->find(2);
        $this->manager->deleteTagRelation($post1);
        $this->manager->deleteTagRelation($post2);
        $count = $this->manager->countTags();
        $this->assertEquals(12, $count);
    }

    public function testFindModelRefererBySlug(){
        $referers = $this->manager->findReferer( 'tag1', 'Post' );
        $this->assertEquals(1, current($referers));
    }
}
