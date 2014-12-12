<?php
/**
 * Created by PhpStorm.
 * User: Rafidion Michael
 * Date: 10/12/2014
 * Time: 22:12
 */

namespace Mykees\TagBundle\Twig\Extension;


use Symfony\Component\DependencyInjection\ContainerInterface;

class TagExtension extends \Twig_Extension {

    protected $container;

    /**
     * Initialize tinymce helper
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getService($id)
    {
        return $this->container->get($id);
    }

    public function getFunctions()
    {
        return array(
            'javascript' => new \Twig_Function_Method($this, 'init', array('is_safe' => array('html'))),
        );
    }

    public function init(){
        return  $this->getService('templating')->render('MykeesTagBundle:Tpl:javascript.html.twig');
    }

    public function getName()
    {
       return "mykees_tag_extension";
    }
}