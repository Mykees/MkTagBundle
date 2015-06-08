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
            'Mvc\BlogBundle\DataFixtures\ORM\LoadPostsData',
        ];
        $this->loadFixtures($fixtures);

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->manager = $this->container->get('mk.tag_manager');
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
        $this->manager->create('jambon fumé');
        $count = $this->manager->countTags();
        $this->assertEquals(1, $count);
    }

    public function testCreateValidArrayTags(){
        $leagueOfLegends = ['Teemo','Corki','Garen','Yi','Akali','Irelia','Twitch'];
        $this->manager->manageTags($leagueOfLegends);
        $count = $this->manager->countTags();
        $this->assertEquals(7, $count);
    }

    public function testAddExistedTags(){
        $leagueOfLegends = ['Teemo','Corki','Garen','Yi','Akali','Irelia','Twitch'];
        $this->manager->manageTags($leagueOfLegends);
        $count = $this->manager->countTags();
        $this->assertEquals(7, $count);


        $existed = ['Corki','Garen','Yi'];
        $this->manager->manageTags($existed);
        $count = $this->manager->countTags();
        $this->assertEquals(7, $count);
    }

    public function testNoValidTags(){
        $noValid = ['         ','         '];
        $this->manager->manageTags($noValid);
        $count = $this->manager->countTags();
        $this->assertEquals(0, $count);
    }

    /*==============*\
      = TagRelation
    \*==============*/

    public function testAddValidRelation(){
        $post = $this->em->getRepository('MvcBlogBundle:Post')->find(17);
        $this->manager->manageTags(['XboxOne','Ps4','Wii U']);
        $this->manager->saveRelation($post);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(3, $count);
    }

    public function testAddExistedRelation(){

        $post = $this->em->getRepository('MvcBlogBundle:Post')->find(21);
        $this->manager->manageTags(['XboxOne','Ps4','Wii U']);
        $this->manager->saveRelation($post);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(3, $count);

        $post = $this->em->getRepository('MvcBlogBundle:Post')->find(21);
        $this->manager->manageTags(['Ps4','Wii U']);
        $this->manager->saveRelation($post);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(3, $count);
    }

    public function testRemoveRelation(){

        $post = $this->em->getRepository('MvcBlogBundle:Post')->find(25);
        $this->manager->manageTags(['XboxOne','Ps4','Wii U']);
        $this->manager->saveRelation($post);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(3, $count);

        $this->manager->deleteTagRelation($post, true);
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount();
        $this->assertEquals(0, $count);
    }

    public function testRemoveUselessTag(){

        $post1 = $this->em->getRepository('MvcBlogBundle:Post')->find(29);
        $this->manager->manageTags(['XboxOne','Ps4','Wii U']);
        $this->save($post1);

        $post2 = $this->em->getRepository('MvcBlogBundle:Post')->find(30);
        $this->manager->manageTags(['Pc','GameBoy','New Nintendo 3DS']);
        $this->save($post2);


        $this->manager->deleteTagRelation($post1,true);
        $this->manager->deleteTagRelation($post2,true);
        $count = $this->manager->countTags();
        $this->assertEquals(0, $count);
    }

    public function testFindModelRefererBySlug(){
        $post = $this->em->getRepository('MvcBlogBundle:Post')->find(33);
        $this->manager->manageTags(['XboxOne','Ps4','Wii U']);
        $this->save($post);

        $referers = $this->manager->findReferer( 'xboxone', 'Post' );
        $this->assertEquals(33, current($referers));
    }

    /**
     * @param $post
     */
    public function save($post)
    {
        $this->manager->saveRelation($post);
    }
}
