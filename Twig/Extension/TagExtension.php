<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 22:12
 */

namespace Mykees\TagBundle\Twig\Extension;

class TagExtension extends \Twig_Extension {


    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('javascript', [$this, 'init'], [
                'is_safe'=>array('html'),
                'needs_environment'=>true
            ])
        );
    }

    public function init(\Twig_Environment $env){
        return  $env->render('MykeesTagBundle:Tpl:javascript.html.twig');
    }

    public function getName()
    {
        return "mykees_tag_extension";
    }
}
