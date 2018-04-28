<?php

namespace Creavo\MultiAppBundle\Entity;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Classes\AppIcon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    public function getAppFieldsFromApp() {

        $serializer=new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);
        $fields=json_decode($this->getFields(),true);

        if($fields===null) {
            return [];
        }

        $data=[];
        foreach($fields AS $field) {
            $data[]=$serializer->deserialize(json_encode($field),AppField::class,'json');
        }

        return $data;
    }

    public function setAppFieldsForApp(array $fields) {

        $serializer=new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);

        $data=[];
        foreach($fields AS $field) {
            $data[]=json_decode($serializer->serialize($field,'json'));
        }

        $this->setFields(json_encode($data,true));
        return $this;
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

    public function getIcon() {
        return $this->icon;
    }

    public function getIconClass(){
        $icons=AppIcon::getChoicesAsObjects();
        if(isset($icons[$this->icon])) {
            return $icons[$this->icon];
        }
        return null;
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
