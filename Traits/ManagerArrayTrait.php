<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 12:34
 */

namespace Mykees\TagBundle\Traits;


use Mykees\TagBundle\Entity\Tag;
use Mykees\TagBundle\Entity\TagRelation;
use Mykees\TagBundle\Interfaces\Taggable;

trait ManagerArrayTrait {

    /**
     * Return all Tags for an array of objects
     * @param array $models
     * @return mixed
     */
    protected function getTagRelationArray( array $models )
    {
        $model_info = $this->getModelInfos($models);

        $query = $this->em->createQueryBuilder()
            ->select('tr')
            ->from($this->tagRelation,'tr')
            ->innerJoin('tr.tag','t')
            ->addSelect('t')
            ->where('tr.model IN(:model)')
            ->andWhere('tr.modelId IN(:modelId)')
            ->setParameter('model',array_values($model_info['models']))
            ->setParameter('modelId',array_values($model_info['ids']))
            ->getQuery()
            ->getResult()
        ;

        return $this->refreshTagsArray($query,$models);
    }

    /**
     * Insert tags in model referer
     * @param array $tagRelations
     * @param array $models
     */
    protected function refreshTagsArray(array $tagRelations, array $models)
    {
        $this->clean($models);
        foreach($models as $model)
        {
            foreach($tagRelations as $tr)
            {
                if( $model instanceof Taggable && $tr->getTag() instanceof Tag )
                {
                    if($model->getId() == $tr->getModelId() && $model->getModel() == $tr->getModel())
                    {
                        $model->getTags()->add($tr->getTag());
                    }
                }
            }
        }
    }

    /**
     * Return an array contain ids and models names
     * @param array $datas
     * @return array
     */
    protected function getModelInfos(array $datas)
    {
        $ids = [];$models = [];$model_exist=false;
        foreach( $datas as $k=>$data )
        {
            if($data instanceof Taggable)
            {
                array_push($ids,$data->getModelId());
                if( $model_exist != $data->getModel() )
                {
                    array_push($models,$data->getModel());
                    $model_exist = $data->getModel();
                }
            }
        }

        return [ 'ids'=>$ids, 'models'=>$models ];
    }

    /**
     * Clean a collection
     * @param array $models
     */
    protected function clean(array $models)
    {
        foreach($models as $model)
        {
            $model->getTags()->clear();
        }
    }
}
