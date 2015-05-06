<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 12:37
 */

namespace Mykees\TagBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Mykees\TagBundle\Entity\Tag;
use Mykees\TagBundle\Interfaces\Taggable;
use Mykees\TagBundle\Util\Reflection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TagListener {

    public $container;
    public $manager;
    public $entity;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow  = $em->getUnitOfWork();
        $this->manager = $this->container->get('mk.tag_manager');

        foreach($uow->getIdentityMap() as $model)
        {
            foreach($model as $entity)
            {
                if ($entity instanceof Tag) {
                    $tags[] = $entity;
                }
                if($entity instanceof Taggable)
                {
                    $this->entity = $entity;
                }
            }
        }
        if($this->entity)
        {
            if(!empty($tags))
            {
                $this->entity->setTags($tags);
                $this->manager->saveRelation($this->entity);
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $model = $args->getEntity();
        $this->manager = $this->container->get('mk.tag_manager');
        
        if( $model instanceof Taggable )
        {
            $this->manager->deleteTagRelation($model);
        }
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postFlush,
            Events::preRemove
        ];
    }
}