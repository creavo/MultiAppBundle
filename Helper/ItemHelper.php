<?php

namespace Creavo\MultiAppBundle\Helper;

use AppBundle\Entity\User;
use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Entity\Activity;
use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Entity\Item;
use Creavo\MultiAppBundle\Entity\ItemRevision;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ItemHelper {

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    public function __construct(Registry $registry) {
        $this->em=$registry->getManager();
    }

    /**
     * creates a new item in the given app
     *
     * @param App $app
     * @param array $data
     * @param User|null $user
     * @param bool $flush
     * @return Item
     */
    public function createItem(App $app, array $data, User $user=null, $flush=true) {

        $normalizer=new Normalizer($this->getAppFieldsFromApp($app));
        $data=$normalizer->transformDataToDatabase($data);

        $item=new Item();
        $item->setApp($app);
        $item->setItemId($this->em->getRepository('CreavoMultiAppBundle:Item')->getNextItemId($app));
        if($user) {
            $item->setCreatedBy($user);
        }

        $itemRevision=new ItemRevision();
        $itemRevision->setType(ItemRevision::TYPE_CREATED);
        $itemRevision->setItem($item);
        $itemRevision->setData($data);
        if($user) {
            $itemRevision->setCreatedBy($user);
        }
        $item->addItemRevision($itemRevision);
        $item->setCurrentRevision($itemRevision);

        $activity=new Activity($item,Activity::TYPE_ITEM_CREATED,$user);
        $activity->setItemRevision($itemRevision);

        $this->em->persist($item);
        $this->em->persist($itemRevision);
        $this->em->persist($activity);

        if($flush) {
            $this->em->flush();
        }
        return $item;
    }

    /**
     * updates an item (only when given data has changed)
     *
     * @param Item $item
     * @param array $data
     * @param User|null $user
     * @param bool $flush
     * @return Item
     */
    public function updateItem(Item $item, array $data, User $user=null, $flush=true) {

        $normalizer=new Normalizer($this->getAppFieldsFromApp($item->getApp()));
        $data=$normalizer->transformDataToDatabase($data);

        $itemRevision=new ItemRevision();
        $itemRevision->setData($data);

        if($itemRevision->getDataHash()===$item->getCurrentRevision()->getDataHash()) {
            // no new data - skip the rest
            return $item;
        }

        $itemRevision->setItem($item);
        $itemRevision->setRevision($this->em->getRepository('CreavoMultiAppBundle:ItemRevision')->getNextRevisionNumber($item));
        $itemRevision->setCreatedBy($user);
        $item->addItemRevision($itemRevision);
        $item->setCurrentRevision($itemRevision);

        $activity=new Activity($item,Activity::TYPE_ITEM_UPDATED,$user);
        $activity->setItemRevision($itemRevision);

        $this->em->persist($itemRevision);
        $this->em->persist($activity);

        if($flush) {
            $this->em->flush();
        }
        return $item;
    }

    /**
     * deletes an item from database
     *
     * @param Item $item
     */
    public function deleteItem(Item $item) {

        $item->setCurrentRevision(null);

        foreach($item->getItemRevisions() AS $itemRevision) {
            $this->em->remove($itemRevision);
        }
        $this->em->flush();

        $this->em->remove($item);
        $this->em->flush();
    }

    /**
     * returns AppFields filled with data from given item
     *
     * @param Item $item
     * @param ItemRevision|null $itemRevision
     * @return array
     */
    public function getItemRow(Item $item, ItemRevision $itemRevision=null) {

        $fields=$this->getAppFieldsFromApp($item->getApp());
        $data=$item->getCurrentRevision()->getData();

        if($itemRevision) {
            $data=$itemRevision->getData();
        }

        $normalizer=new Normalizer($this->getAppFieldsFromApp($item->getApp()));
        $data=$normalizer->transformDataToPhp($data);

        /** @var AppField $field */
        foreach($fields AS $field) {
            if(isset($data[$field->getSlug()])) {
                $field->setData($data[$field->getSlug()]);
            }
        }

        return $fields;
    }

    public function getAppFieldsFromApp(App $app) {

        $serializer=new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);
        $fields=json_decode($app->getFields(),true);

        if($fields===null) {
            return [];
        }

        $data=[];
        foreach($fields AS $field) {
            $data[]=$serializer->deserialize(json_encode($field),AppField::class,'json');
        }

        return $data;
    }

    public function setAppFieldsForApp(App $app, array $fields) {

        $serializer=new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);

        $data=[];
        foreach($fields AS $field) {
            $data[]=json_decode($serializer->serialize($field,'json'));
        }

        $app->setFields(json_encode($data,true));
        return $app;
    }

}