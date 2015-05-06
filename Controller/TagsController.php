<?php

namespace Mykees\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TagsController extends Controller
{
    public function manage(){
        return $this->getDoctrine()->getManager();
    }
    public function deleteRelationAction( $relation_id ){
        $tagRelation = $this->manage()->getRepository('MykeesTagBundle:TagRelation')->find($relation_id);
        $this->manage()->remove($tagRelation);
        $this->manage()->flush();
        $tag_id = $tagRelation->getTag()->getId();
        $manager = $this->container->get('mk.tag_manager');
        $manager->useless($tag_id);
        return new Response();
    }
}
