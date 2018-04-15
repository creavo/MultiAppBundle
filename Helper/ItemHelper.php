<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Entity\Activity;
use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Entity\Item;
use Creavo\MultiAppBundle\Entity\ItemRevision;
use Creavo\MultiAppBundle\Interfaces\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ItemHelper {

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    protected $userRepository;

    /** @var array */
    protected $relations;

    public function __construct(Registry $registry, $userClass=null, array $relations=[]) {
        $this->em=$registry->getManager();

        if($userClass) {
            $this->userRepository=$this->em->getRepository($userClass);
        }

        foreach($relations AS $key=>$relation) {
            $relations[$key]['repository']=$this->em->getRepository($relation['class']);
        }
        $this->relations=$relations;
    }

    /**
     * creates a new item in the given app
     *
     * @param App $app
     * @param array $data
     * @param UserInterface|null $user
     * @param bool $flush
     * @return Item
     */
    public function createItem(App $app, array $data, UserInterface $user=null, $flush=true) {

        $normalizer=new Normalizer($this->getAppFieldsFromApp($app));
        $data=$normalizer->transformDataToDatabase($data);

        $item=new Item();
        $item->setApp($app);
        $item->setItemId($this->em->getRepository('CreavoMultiAppBundle:Item')->getNextItemId($app));
        if($user) {
            $item->setCreatedBy($user->getId());
        }

        $itemRevision=new ItemRevision();
        $itemRevision->setType(ItemRevision::TYPE_CREATED);
        $itemRevision->setItem($item);
        $itemRevision->setData($data);
        if($user) {
            $itemRevision->setCreatedBy($user->getId());
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
     * @param UserInterface|null $user
     * @param bool $flush
     * @return Item
     */
    public function updateItem(Item $item, array $data, UserInterface $user=null, $flush=true) {

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
        if($user) {
            $itemRevision->setCreatedBy($user->getId());
        }
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
     * hard-deletes / remove really an item
     *
     * @param Item $item
     * @param UserInterface|null $user
     */
    public function hardDeleteItem(Item $item, UserInterface $user=null) {

        $item->setCurrentRevision(null);

        foreach($item->getItemRevisions() AS $itemRevision) {
            $this->em->remove($itemRevision);
        }
        $this->em->flush();

        $this->em->remove($item);
        $this->em->flush();
    }

    /**
     * soft-deletes an item
     *
     * @param Item $item
     * @param UserInterface|null $user
     * @return Item
     */
    public function softDeleteItem(Item $item, UserInterface $user=null) {

        $item->setDeletedAt(new \DateTime('now'));

        $activity=new Activity($item,Activity::TYPE_ITEM_DELETED,$user);
        $this->em->persist($activity);
        $this->em->flush();
        return $item;
    }

    /**
     * restores an soft-deleted item
     *
     * @param Item $item
     * @param UserInterface|null $user
     * @return Item
     */
    public function restoreItem(Item $item, UserInterface $user=null) {

        $item->setDeletedAt(null);

        $activity=new Activity($item,Activity::TYPE_ITEM_RESTORED,$user);
        $this->em->persist($activity);
        $this->em->flush();
        return $item;
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

        $return=[];

        /** @var AppField $field */
        foreach($fields AS $key=>$field) {
            $return[$field->getSlug()]=$field;
            if(isset($data[$field->getSlug()])) {
                $field->setData($data[$field->getSlug()]);
            }
        }

        return $return;
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

    /**
     * get user-entity by id
     *
     * @param $id
     * @return null|UserInterface
     */
    public function getUserById($id) {

        if($id===null) {
            return null;
        }

        if(
            $this->userRepository AND
            $user=$this->userRepository->find($id) AND
            $user instanceof UserInterface
        ) {
            return $user;
        }

        return null;
    }

    public function generateDiffFromItemRevisions(ItemRevision $first, ItemRevision $second) {

        if($first->getItem()!==$second->getItem()) {
            throw new \Exception('item-revisions must be from the same item to compare them');
        }

        $changes=[];
        $firstAppFields=$this->getItemRow($first->getItem(),$first);
        $secondAppFields=$this->getItemRow($first->getItem(),$second);

        /** @var AppField $appField */
        foreach($firstAppFields AS $slug=>$appField) {
            if(!isset($secondAppFields[$slug])) {
                $changes[$slug]=[
                    'name'=>$appField->getName(),
                    'first'=>null,
                    'second'=>$appField,
                ];
                continue;
            }

            /** @var AppField $secondAppField */
            $secondAppField=$secondAppFields[$slug];

            if($appField->getData()!==$secondAppField->getData()) {
                $changes[$slug]=[
                    'name'=>$appField->getName(),
                    'first'=>$appField,
                    'second'=>$secondAppField,
                ];
            }
        }

        return $changes;
    }

}