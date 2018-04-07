<?php

namespace Creavo\MultiAppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App
 *
 * @ORM\Table(name="crv_ma_apps")
 * @ORM\Entity(repositoryClass="Creavo\MultiAppBundle\Repository\AppRepository")
 */
class App
{

    const ICONS=[
        'glyphicon glyphicon-asterisk'=>1,
        'glyphicon glyphicon-cloud'=>2,
        'glyphicon glyphicon-envelope'=>3,
        'glyphicon glyphicon-music'=>4,
        'glyphicon glyphicon-heart'=>5,
        'glyphicon glyphicon-star'=>6,
        'glyphicon glyphicon-user'=>7,
        'glyphicon glyphicon-film'=>8,
        'glyphicon glyphicon-cog'=>9,
        'glyphicon glyphicon-road'=>10,
        'glyphicon glyphicon-time'=>11,
        'glyphicon glyphicon-stats'=>12,
        'glyphicon glyphicon-king'=>13,
        'glyphicon glyphicon-lamp'=>14,
        'glyphicon glyphicon-piggy-bank'=>15,
    ];

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
     * @var string
     *
     * @ORM\Column(name="item_singular_name", type="string", length=64)
     */
    private $itemSingularName;

    /**
     * @var string
     *
     * @ORM\Column(name="item_plural_name", type="string", length=64)
     */
    private $itemPluralName;

    /**
     * @var string
     *
     * @ORM\Column(name="fields", type="text", nullable=true)
     */
    private $fields;

    /**
     * @var Item[]
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="app")
     */
    private $items;

    /**
     * @var Workspace
     *
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="apps")
     */
    private $workspace;

    /**
     * @var int
     *
     * @ORM\Column(name="icon", type="smallint", nullable=true)
     */
    private $icon;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


    public function __construct($name,$slug){

        $this->setName($name);
        $this->setSlug($slug);

        $this->setItemSingularName('Element');
        $this->setItemSingularName('Elemente');

        $this->setCreatedAt(new \DateTime('now'));

        $this->items=new ArrayCollection();
    }

    public function __toString() {
        return $this->getName();
    }

    public function getId(){
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

    public function addItem(\Creavo\MultiAppBundle\Entity\Item $item){
        $this->items[] = $item;
        return $this;
    }

    public function removeItem(\Creavo\MultiAppBundle\Entity\Item $item){
        return $this->items->removeElement($item);
    }

    public function getItems(){
        return $this->items;
    }

    public function setWorkspace(\Creavo\MultiAppBundle\Entity\Workspace $workspace = null){
        $this->workspace = $workspace;
        return $this;
    }

    public function getWorkspace(){
        return $this->workspace;
    }

    public function setItemSingularName($itemSingularName){
        $this->itemSingularName = $itemSingularName;
        return $this;
    }

    public function getItemSingularName(){
        return $this->itemSingularName;
    }

    public function setItemPluralName($itemPluralName){
        $this->itemPluralName = $itemPluralName;
        return $this;
    }

    public function getItemPluralName(){
        return $this->itemPluralName;
    }

    public function setFields($fields){
        $this->fields = $fields;
        return $this;
    }

    public function getFields(){
        return $this->fields;
    }

    public function getIcon(){
        return $this->icon;
    }

    public function setIcon($icon){
        $this->icon = $icon;
        return $this;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt){
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(){
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt){
        $this->updatedAt = $updatedAt;
        return $this;
    }




}
