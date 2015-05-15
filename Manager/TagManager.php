<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 13:16
 */

namespace Mykees\TagBundle\Manager;


use Mykees\TagBundle\Util\Urlizer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mykees\TagBundle\Interfaces\Taggable;
use Mykees\TagBundle\Traits\ManagerArrayTrait;
use Mykees\TagBundle\Traits\ManagerObjectTrait;
use Mykees\TagBundle\Entity\Tag;
use Mykees\TagBundle\Entity\TagRelation;

class TagManager {

    public $em;
    public $tag;
    public $tagRelation;

    use ManagerArrayTrait, ManagerObjectTrait;

    public function __construct( EntityManager $em, $tag=null, $tagRelation=null )
    {
        $this->em = $em;
        $this->tag = $tag ?: 'Mykees\TagBundle\Entity\Tag';
        $this->tagRelation = $tagRelation ?: 'Mykees\TagBundle\Entity\TagRelation';
    }



    public function findTagRelation( $model )
    {
        if(is_array($model))
        {
            $this->getTagRelationArray($model);
        }else{
            $this->getTagRelation($model);
        }
    }

    /**
     * Remove relation between Tag and a model given
     * @param Taggable $model
     * @internal param $id
     */
    public function deleteTagRelation( Taggable $model )
    {
        $tagRelationList = $this->em->createQueryBuilder()
            ->select('t')
            ->from($this->tagRelation, 't')
            ->where('t.model = :type')
            ->setParameter('type', $model->getModel())
            ->andWhere('t.modelId = :id')
            ->setParameter('id', $model->getModelId())
            ->getQuery()
            ->getResult();
        $model->setRemove(true);

        foreach($tagRelationList as $relation)
        {
            $this->em->remove($relation);
            $this->em->flush();
            $this->useless($relation->getTag()->getId());
        }
    }

    /**
     * Remove tag
     * @param $tag_id
     */
    public function delete( $tag_id )
    {
        $tag = $this->em->createQueryBuilder()
            ->select('t')
            ->from($this->tag, 't')
            ->where('t.id = :id')
            ->setParameter('id', $tag_id)
            ->getQuery()
            ->getOneOrNullResult();
        $this->em->remove($tag);
        $this->em->flush();
    }

    public function deleteByName($name)
    {

        if(is_array($name))
        {
            $names = $this->findByName($names);
            foreach($names as $n)
            {
                $this->em->remove($n);
                $this->em->flush();
            }
        }else{
            $tag = $this->em->createQueryBuilder()
                ->select('t')
                ->from($this->tag, 't')
                ->where('t.id = :id')
                ->setParameter('id', $tag_id)
                ->getQuery()
                ->getOneOrNullResult();
            $this->em->remove($tag);
            $this->em->flush();
        }
    }

    /**
     * Save Tag Relation and add create and save tags if don't exist
     * @param Taggable $model
     */
    public function saveRelation( Taggable $model )
    {
        if(!$model->getRemove())
        {
            if($model->getTags()->count() > 0)
            {
                $addedTags = $model->getTags();
                $model_id = $model->getModelId();
                $model_name = $model->getModel();
                $addNewTags = $addedTags;

                $tagsRelationExisted = $this->findRelationByTagName($model->getTags(),$model);

                //Remove existed Tag from collection
                foreach($tagsRelationExisted as $tagExisted)
                {
                    if($addedTags->exists(function($index,$addedTag) use ($tagExisted)
                    {
                        return $addedTag->getName() === $tagExisted->getName();
                    })){
                        $addNewTags->removeElement($tagExisted);
                    }
                }

                //Save
                foreach($addNewTags as $tag)
                {
                    $relation = new TagRelation();
                    $relation->setModel($model_name);
                    $relation->setModelId($model_id);
                    $relation->setTag($tag);
                    $this->em->persist($relation);
                }

                if (count($addNewTags) > 0)
                {
                    $this->em->flush();
                }
            }
        }

    }

    /**
     * Return Tag by a name
     * @param array $names
     */
    public function findByName( $names )
    {
        $q = $this->tagsQuery();
        $query = $this->addNameConstraint($q,$names);

        return $query->getQuery()->getResult();
    }

    /**
     * Return ids of model type linked with a tag
     * @param $slug
     * @param null $modelType
     * @return array
     */
    public function findReferer( $slug, $modelType=null )
    {
        $modelRefererIds = [];

        $q = $this->em->createQueryBuilder()
            ->select('tr')
            ->from($this->tagRelation,'tr')
            ->innerJoin('tr.tag','t')
            ->addSelect('t')
            ->where('t.slug = :slug')
            ->setParameter('slug',$slug)
        ;

        $query = $this->addModelTypeConstraint($q,$modelType);
        $result = $query->getQuery()->getResult();

        foreach($result as $tr)
        {
            array_push($modelRefererIds,$tr->getModelId());
        }

        return $modelRefererIds;
    }

    /**
     * Delete tag unused
     * @param $tag_id
     */
    public function useless($tag_id)
    {
        $count = $this->em->getRepository('MykeesTagBundle:TagRelation')->findCount($tag_id);
        if($count == 0)
        {
            $this->delete($tag_id);
        }
    }

    public function create( $name )
    {
        $tag = new Tag();
        $tag->setName($name);
        $tag->setSlug(Urlizer::urlize($name));
        $this->em->persist($tag);
        $this->em->flush();

        return $tag;
    }

    public function countTags()
    {
      return $this->em->getRepository('MykeesTagBundle:Tag')->findCount();
    }

    public function manageTags( $names )
    {
        foreach($names as $k=>$name)
        {
            $name = trim($name);
            if(!empty($name))
            {
                $q = $this->queryTag();
                $query = $this->addNameConstraint($q,$name);
                $tagExist = $query->getQuery()->getOneOrNullResult();

                if(!empty($tagExist))
                {
                    $names[$k] = $tagExist;
                }else{
                    $tag = $this->create($name);
                    $names[$k] = $tag;
                }
            }else{
                unset($names[$k]);
            }
        }

        return $names;
    }
}