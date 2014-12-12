<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 12:34
 */

namespace Mykees\TagBundle\Traits;


trait LoadManagerTrait {

    public function tagManager(){
        return $this->get('mk.tag.manager');
    }
} 