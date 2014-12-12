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
use Mykees\TagBundle\Interfaces\Taggable;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TagListener {

    public $container;
    public $manager;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow  = $em->getUnitOfWork();
        $cmf  = $em->getMetadataFactory();
        $this->manager = $this->container->get('mk.tag.manager');

        if( current($uow->getIdentityMap()) ){
          foreach(current($uow->getIdentityMap()) as $model){
              if( $model instanceof Taggable){
                  $this->manager->saveRelation($model);
              }
          }
        }

    }

    public function preRemove(LifecycleEventArgs $args){
        $model = $args->getEntity();
        $this->manager = $this->container->get('mk.tag.manager');
        if( $model instanceof Taggable ){
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