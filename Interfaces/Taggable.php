<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 12:33
 */

namespace Mykees\TagBundle\Interfaces;


interface Taggable {
    /**
     * Return the referer model
     * @return mixed
     */
    public function getModel();

    /**
     * Return the referer model id
     * @return mixed
     */
    public function getModelId();

    /**
     * Return all tags of referer model
     * @return mixed
     */
    public function getTags();
} 