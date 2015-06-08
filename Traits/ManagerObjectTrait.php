<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 12:33
 */

namespace Mykees\TagBundle\Traits;

use Mykees\TagBundle\Util\Urlizer;
use Mykees\TagBundle\Entity\Tag;
use Mykees\TagBundle\Entity\TagRelation;
use Mykees\TagBundle\Interfaces\Taggable;
use Doctrine\ORM\Query\Expr;

trait ManagerObjectTrait {

    /**
     * Insert one Tag in model referer
     * @param Tag $tag
     * @param Taggable $model
     */
    protected function addTag(Tag $tag, Taggable $model)
    {
        $model->getTags()->add($tag);
    }

    /**
     * Insert several Tags in model referer
     * @param array $tags
     * @param Taggable $model
     */
    protected function addTags(array $tags, Taggable $model)
    {
        foreach($tags as $tag)
        {
            if(!empty($tag) && $tag instanceof Tag)
            {
                $this->addTag($tag,$model);
            }
        }
    }

    /**
     * Remove and insert tags in model referer
     * @param array $tags
     * @param Taggable $model
     */
    protected function refreshTags(array $tags, Taggable $model)
    {
        $model->getTags()->clear();
        $this->addTags($tags,$model);
    }

    /**
     * Get Tags and Relations
     * @param Taggable $model
     */
    protected function getTagRelation(Taggable $model)
    {
        $query = $this->queryRelation($model);

        return $this->refreshTags($query,$model);
    }

    protected function queryTag()
    {
        $query = $this->em->createQueryBuilder()
            ->select('t')
            ->from($this->tag,'t')
        ;

        return $query;
    }

    protected function slugify(array $datas)
    {
        $datas = array_unique($datas);
        foreach($datas as $k=>$d)
        {
            $datas[$k] = Urlizer::urlize($d);
        }

        return $datas;
    }

    protected function addNameConstraint($query,$names)
    {
        if($names)
        {
            if(is_array($names))
            {
                $names = $this->slugify($names);
                $query->where($query->expr()->in('t.slug', $names));
            }else{
                $query->andWhere('t.slug = :slug')
                    ->setParameter('slug',Urlizer::urlize($names))
                ;
            }
        }
        return $query;
    }

    protected function addModelTypeConstraint($query,$modelType)
    {
        if($modelType)
        {
            if(is_array($modelType))
            {
                $modelType = $this->slugify($modelType);
                $query->andWhere($query->expr()->in('tr.model', $modelType));
            }else{
                $query->andWhere('tr.model = :model')
                    ->setParameter('model',$modelType)
                ;
            }
        }
        return $query;
    }

    protected function queryRelation($model)
    {
        $query = $this->em->createQueryBuilder()
            ->select('t')
            ->from($this->tag,'t')
            ->innerJoin('t.tagRelation','tg',Expr\Join::WITH, 'tg.model = :model AND tg.modelId = :modelId')
            ->addSelect('tg')
            ->setParameter('model',$model->getModel())
            ->setParameter('modelId',$model->getModelId())
            ->getQuery()
            ->getResult()
        ;

        return $query;
    }

    protected function findRelationByTagName($names, $model)
    {
        $slugified = [];
        foreach($names as $name)
        {
            $slugified[] = $name->getSlug();
        }

        $query = $this->em->createQueryBuilder()
            ->select('t')
            ->from($this->tag,'t')
            ->innerJoin('t.tagRelation','tg',Expr\Join::WITH, 'tg.model = :model AND tg.modelId = :modelId')
            ->addSelect('tg')
            ->where($this->em->createQueryBuilder()->expr()->in('t.slug', $slugified))
            ->setParameter('model',$model->getModel())
            ->setParameter('modelId',$model->getModelId())
            ->getQuery()
            ->getResult()
        ;
        
        return $query;
    }
}
