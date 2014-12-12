<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 11/12/2014
 * Time: 00:41
 */

namespace Mykees\TagBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TagRelationRepository extends EntityRepository {


    public function findCount($tag_id=null){

        if($tag_id){
            return $this->_em->createQueryBuilder()
                ->select('count(tr.tag)')
                ->from('Mykees\TagBundle\Entity\TagRelation','tr')
                ->where('tr.tag = :id')
                ->setParameter('id',$tag_id)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }else{
            return $this->_em->createQueryBuilder()
                ->select('count(tr.id)')
                ->from('Mykees\TagBundle\Entity\TagRelation','tr')
                ->getQuery()
                ->getSingleScalarResult()
            ;
        }
    }
} 