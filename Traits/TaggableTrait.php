<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 14:08
 */

namespace Mykees\TagBundle\Traits;


use Doctrine\Common\Collections\ArrayCollection;
use Mykees\TagBundle\Util\Reflection;

trait TaggableTrait {

    public function getModel(){
        return Reflection::getClassShortName($this);
    }

    public function getModelId(){
        return $this->getId();
    }

    public function getTags(){
        $this->tags = $this->tags ?: new ArrayCollection();
        return $this->tags;
    }
} 