<?php

namespace Creavo\MultiAppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="crv_ma_items", indexes={
 *      @ORM\Index(name="item_id_idx", columns={"item_id"}),
 *      @ORM\Index(name="search_idx", columns={"app_id","item_id"})
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
     * @var ItemRevision
     *
     * @ORM\OneToOne(targetEntity="ItemRevision")
     */
    private $currentRevision;

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

    /**
     * @var ItemRevision[]
     *
     * @ORM\OneToMany(targetEntity="ItemRevision", mappedBy="item")
     */
    private $itemRevisions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var Activity[]
     *
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="item")
     */
    private $activities;


    public function __construct() {
        $this->itemRevisions=new ArrayCollection();
        $this->activities=new ArrayCollection();
        $this->createdAt=new \DateTime('now');
    }

    public function __toString() {
        return 'Item '.$this->getId();
    }

    public function getId(){
        return $this->id;
    }

    public function setApp(\Creavo\MultiAppBundle\Entity\App $app = null){
        $this->app = $app;
        return $this;
    }

    public function getApp(){
        return $this->app;
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

    public function setCurrentRevision(\Creavo\MultiAppBundle\Entity\ItemRevision $currentRevision = null){
        $this->currentRevision = $currentRevision;
        return $this;
    }

    public function getCurrentRevision(){
        return $this->currentRevision;
    }

    public function addItemRevision(\Creavo\MultiAppBundle\Entity\ItemRevision $itemRevision){
        $this->itemRevisions[] = $itemRevision;
        return $this;
    }

    public function removeItemRevision(\Creavo\MultiAppBundle\Entity\ItemRevision $itemRevision){
        $this->itemRevisions->removeElement($itemRevision);
    }

    public function getItemRevisions(){
        return $this->itemRevisions;
    }

    public function getDeletedAt(){
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt){
        $this->deletedAt = $deletedAt;
        return $this;
    }



    /**
     * Add activity.
     *
     * @param \Creavo\MultiAppBundle\Entity\Activity $activity
     *
     * @return Item
     */
    public function addActivity(\Creavo\MultiAppBundle\Entity\Activity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity.
     *
     * @param \Creavo\MultiAppBundle\Entity\Activity $activity
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeActivity(\Creavo\MultiAppBundle\Entity\Activity $activity)
    {
        return $this->activities->removeElement($activity);
    }

    /**
     * Get activities.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }
}
