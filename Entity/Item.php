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


    public function __construct() {

    }

    public function getId()
    {
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
}
