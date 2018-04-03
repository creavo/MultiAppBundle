<?php

namespace Creavo\MultiAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="crv_ma_items")
 * @ORM\Entity(repositoryClass="Creavo\MultiAppBundle\Repository\ItemRepository")
 */
class Item
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
     * @var App
     *
     * @ORM\ManyToOne(targetEntity="App", inversedBy="items")
     */
    private $app;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="json_array")
     */
    private $data;

    /**
     * @var int
     *
     * @ORM\Column(name="revision", type="integer")
     */
    private $revision=1;

    /**
     * @var bool
     *
     * @ORM\Column(name="current_revision", type="boolean")
     */
    private $currentRevision=false;


    public function __construct() {

    }

    public function getId(){
        return $this->id;
    }

    public function setData($data){
        $this->data = $data;
        return $this;
    }

    public function getData(){
        return $this->data;
    }

    public function setApp(\Creavo\MultiAppBundle\Entity\App $app = null){
        $this->app = $app;
        return $this;
    }

    public function getApp(){
        return $this->app;
    }

    public function getRevision(){
        return $this->revision;
    }

    public function setRevision($revision){
        $this->revision = $revision;
        return $this;
    }

    public function isCurrentRevision(){
        return $this->currentRevision;
    }

    public function getCurrentRevision(){
        return $this->currentRevision;
    }

    public function setCurrentRevision($currentRevision){
        $this->currentRevision = $currentRevision;
        return $this;
    }
}
