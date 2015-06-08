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
use Doctrine\Common\Persistence\ManagerRegistry;
use Mykees\TagBundle\Manager\TagManager;

class TagListener {

    public $managerRegistry;
    public $entity;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow  = $em->getUnitOfWork();
        $manager = new TagManager($this->managerRegistry);

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
                $manager->saveRelation($this->entity);
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $model = $args->getEntity();
        $manager = new TagManager($this->managerRegistry);

        if( $model instanceof Taggable )
        {
            $model->setRemove(true);
            $manager->deleteTagRelation($model);
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
