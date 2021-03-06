<?php

namespace Mykees\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mykees\TagBundle\Repository\TagRepository")
 */
class Tag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="Mykees\TagBundle\Entity\TagRelation",mappedBy="tag", cascade={"persist", "merge", "remove"}, orphanRemoval=true)
     */
    private $tagRelation;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Tag
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tagRelation = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tagRelation
     *
     * @param \Mykees\TagBundle\Entity\TagRelation $tagRelation
     * @return Tag
     */
    public function addTagRelation(\Mykees\TagBundle\Entity\TagRelation $tagRelation)
    {
        $this->tagRelation[] = $tagRelation;

        return $this;
    }

    /**
     * Remove tagRelation
     *
     * @param \Mykees\TagBundle\Entity\TagRelation $tagRelation
     */
    public function removeTagRelation(\Mykees\TagBundle\Entity\TagRelation $tagRelation)
    {
        $this->tagRelation->removeElement($tagRelation);
    }

    /**
     * Get tagRelation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagRelation()
    {
        return $this->tagRelation;
    }
}
