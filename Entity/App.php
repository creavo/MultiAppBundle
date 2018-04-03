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


    public function __construct($name,$slug){

        $this->setName($name);
        $this->setSlug($slug);

        $this->setItemSingularName('Element');
        $this->setItemSingularName('Elemente');

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
}
