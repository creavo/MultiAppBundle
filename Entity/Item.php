<?php

namespace Creavo\MultiAppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="crv_ma_items", indexes={
 *      @ORM\Index(name="item_id_idx", columns={"item_id"}),
 *      @ORM\Index(name="search_idx", columns={"app_id","item_id","current_revision"})
 * })
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
     * @var int
     *
     * @ORM\Column(name="item_id", type="integer")
     */
    private $itemId;

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

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $createdBy;


    public function __construct() {
        $this->createdAt=new \DateTime('now');
    }

    public function getDataHash() {
        if($this->getData()) {
            return md5(json_encode($this->getData()));
        }

        return null;
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

    public function getItemId(){
        return $this->itemId;
    }

    public function setItemId($itemId){
        $this->itemId = $itemId;
        return $this;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt){
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null){
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedBy(){
        return $this->createdBy;
    }
}
