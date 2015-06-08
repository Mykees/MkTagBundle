<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 11/12/2014
 * Time: 00:31
 */

namespace Mykees\TagBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository {

    public function findCount()
    {
        return $this->_em->createQueryBuilder()
            ->select('count(t.id)')
            ->from('Mykees\TagBundle\Entity\Tag','t')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
