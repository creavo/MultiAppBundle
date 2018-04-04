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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $createdBy;


    public function __construct() {
        $this->createdAt=new \DateTime('now');
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

    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null){
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedBy(){
        return $this->createdBy;
    }
}