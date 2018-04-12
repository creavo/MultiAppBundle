<?php

namespace Creavo\MultiAppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * ItemRevision
 *
 * @ORM\Table(name="crv_ma_item_revisions")
 * @ORM\Entity(repositoryClass="Creavo\MultiAppBundle\Repository\ItemRevisionRepository")
 */
class ItemRevision
{

    const TYPE_CREATED=1;
    const TYPE_UPDATED=2;
    const TYPE_MARKED_AS_DELETED=3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="itemRevisions")
     */
    private $item;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;


    public function __construct() {
        $this->createdAt=new \DateTime('now');
        $this->setType(self::TYPE_UPDATED);
    }

    public function __toString() {
        return 'ItemRevision '.$this->getId();
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

    public function setRevision($revision){
        $this->revision = $revision;
        return $this;
    }

    public function getRevision(){
        return $this->revision;
    }

    public function setCreatedAt($createdAt){
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

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
        return $this;
    }

    public function setCreatedBy($createdBy){
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedBy(){
        return $this->createdBy;
    }
}
