<?php

namespace Creavo\MultiAppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="crv_ma_activities")
 * @ORM\Entity(repositoryClass="Creavo\MultiAppBundle\Repository\ActivityRepository")
 */
class Activity {

    const TYPE_ITEM_CREATED=1;
    const TYPE_ITEM_UPDATED=2;
    const TYPE_ITEM_DELETED=3;
    const TYPE_COMMENT=4;
    const TYPE_ITEM_RESTORED=5;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

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
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="activities")
     */
    private $item;

    /**
     * @var ItemRevision
     *
     * @ORM\OneToOne(targetEntity="ItemRevision")
     */
    private $itemRevision;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=2048, nullable=true)
     */
    private $comment;


    public function __construct(Item $item, $type, User $createdBy=null) {
        $this->setItem($item);
        $this->setType($type);
        $this->setCreatedBy($createdBy);
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function __toString() {

        $itemSingularName=$this->item->getApp()->getItemSingularName();
        $user='Anonym';
        if($this->createdBy) {
            $user=$this->createdBy->__toString();
        }

        if($this->getType()===self::TYPE_ITEM_CREATED) {
            return $itemSingularName.' wurde erstellt';
        }

        if($this->getType()===self::TYPE_ITEM_UPDATED) {
            return $itemSingularName.' wurde bearbeitet';
        }

        if($this->getType()===self::TYPE_ITEM_DELETED) {
            return $itemSingularName.' wurde gelöscht';
        }

        if($this->getType()===self::TYPE_ITEM_RESTORED) {
            return $itemSingularName.' wurde wiederhergestellt';
        }

        if($this->getType()===self::TYPE_COMMENT) {
            //return $user.' kommentierte';
        }

        return '';
    }

    public function getId(){
        return $this->id;
    }

    public function setType($type){
        $this->type = $type;
        return $this;
    }

    public function getType(){
        return $this->type;
    }

    public function setCreatedAt($createdAt = null){
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function setItem(\Creavo\MultiAppBundle\Entity\Item $item = null){
        $this->item = $item;
        return $this;
    }

    public function getItem(){
        return $this->item;
    }

    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null){
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedBy(){
        return $this->createdBy;
    }

    public function setItemRevision(\Creavo\MultiAppBundle\Entity\ItemRevision $itemRevision = null){
        $this->itemRevision = $itemRevision;
        return $this;
    }

    public function getItemRevision(){
        return $this->itemRevision;
    }

    public function setComment($comment = null){
        $this->comment = $comment;
        return $this;
    }

    public function getComment(){
        return $this->comment;
    }
}
