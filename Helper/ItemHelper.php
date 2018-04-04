<?php

namespace Creavo\MultiAppBundle\Helper;

use AppBundle\Entity\User;
use Creavo\MultiAppBundle\Classes\AppField;
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

    public function createItem(App $app, array $data, User $user=null, $flush=true) {

        $item=new Item();
        $item->setApp($app);
        $item->setItemId($this->em->getRepository('CreavoMultiAppBundle:Item')->getNextItemId($app));
        if($user) {
            $item->setCreatedBy($user);
        }

        $itemRevision=new ItemRevision();
        $itemRevision->setItem($item);
        $itemRevision->setData($data);
        if($user) {
            $itemRevision->setCreatedBy($user);
        }
        $item->addItemRevision($itemRevision);
        $item->setCurrentRevision($itemRevision);

        $this->em->persist($item);
        $this->em->persist($itemRevision);

        if($flush) {
            $this->em->flush();
        }
        return $item;
    }

    public function updateItem(Item $item, array $data, User $user=null, $flush=true) {

        $itemRevision=new ItemRevision();
        $itemRevision->setData($data);

        if($itemRevision->getDataHash()===$item->getCurrentRevision()->getDataHash()) {
            // no new data - skip the rest
            return $item;
        }

        $itemRevision->setItem($item);
        $itemRevision->setRevision($this->em->getRepository('CreavoMultiAppBundle:ItemRevision')->getNextRevisionNumber($item));
        if($user) {
            $itemRevision->setCreatedBy($user);
        }
        $item->addItemRevision($itemRevision);
        $item->setCurrentRevision($itemRevision);

        $this->em->persist($itemRevision);

        if($flush) {
            $this->em->flush();
        }
        return $item;
    }

    public function getItemRow(Item $item, ItemRevision $itemRevision=null) {

        $fields=$this->getAppFieldsFromApp($item->getApp());
        $data=$item->getCurrentRevision()->getData();

        if($itemRevision) {
            $data=$itemRevision->getData();
        }

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

        $data=[];
        foreach($fields AS $field) {
            $data[]=$serializer->deserialize(json_encode($field),AppField::class,'json');
        }

        return $data;
    }

    protected function setAppFieldsForApp(App $app, array $fields) {

        $serializer=new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);

        $data=[];
        foreach($fields AS $field) {
            $data[]=json_decode($serializer->serialize($field,'json'));
        }

        $app->setFields(json_encode($data,true));
        return $app;
    }

}