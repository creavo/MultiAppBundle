<?php

namespace Creavo\MultiAppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Workspace
 *
 * @ORM\Table(name="crv_ma_workspaces")
 * @ORM\Entity(repositoryClass="Creavo\MultiAppBundle\Repository\WorkspaceRepository")
 */
class Workspace
{
    /**
     * @var int
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
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var App[]
     *
     * @ORM\OneToMany(targetEntity="App", mappedBy="workspace")
     */
    private $apps;


    public function __construct() {
        $this->apps=new ArrayCollection();
    }

    public function __toString() {
        return $this->getName();
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getName(){
        return $this->name;
    }

    public function setSlug($slug){
        $this->slug = $slug;
        return $this;
    }

    public function getSlug(){
        return $this->slug;
    }

    public function addApp(\Creavo\MultiAppBundle\Entity\App $app){
        $this->apps[] = $app;
        return $this;
    }

    public function removeApp(\Creavo\MultiAppBundle\Entity\App $app){
        return $this->apps->removeElement($app);
    }

    public function getApps(){
        return $this->apps;
    }
}
