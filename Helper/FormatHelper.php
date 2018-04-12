<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Interfaces\AbstractEntityInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Routing\RouterInterface;

class FormatHelper {

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    /** @var array */
    protected $relations;

    /** @var RouterInterface */
    protected $router;

    public function __construct(Registry $registry, RouterInterface $router, array $relations=[]) {
        $this->em=$registry->getManager();
        $this->router=$router;

        foreach($relations AS $key=>$relation) {
            $relations[$key]['repository']=$this->em->getRepository($relation['class']);
        }
        $this->relations=$relations;
    }

    public function renderAppFieldData(AppField $appField, $default=null, $html=true) {

        $type=$appField->getType();

        if($appField->getData()===null) {
            return $default;
        }

        switch($type) {

            case AppField::TYPE_STRING:

                break;

            case AppField::TYPE_CHOICE:
                if(isset($appField->getChoices()[$appField->getData()])) {
                    return $appField->getChoices()[$appField->getData()];
                }
                return 'Unbekannte Option';
                break;

            case AppField::TYPE_NUMBER:
                return number_format($appField->getData(),$appField->getScale(),',','.');
                break;

            case AppField::TYPE_MONEY:
                return number_format($appField->getData(),$appField->getScale(),',','.').' '.$appField->getCurrency();
                break;

            case AppField::TYPE_URL:
                if($html) {
                    return '<a href="'.$appField->getData().'" target="_blank">'.$appField->getData().'</a>';
                }
                break;

            case AppField::TYPE_EMAIL:
                if($html) {
                    return '<a href="mailto:'.$appField->getData().'">'.$appField->getData().'</a>';
                }
                break;

            case AppField::TYPE_PROGRESS:
                if($html) {
                    return '<div class="progress" style="margin-bottom:0;"><div class="progress-bar" style="min-width:2em;width:'.$appField->getData().'%;">'.$appField->getData().'%</div></div>';
                }
                return $appField->getData().'%';
                break;

            case AppField::TYPE_DATETIME:
                return $appField->getData()->format('d.m.Y H:i:s');
                break;

            case AppField::TYPE_BOOLEAN:
                if($appField->getData()===true) {
                    return 'Ja';
                }
                if($appField->getData()===false) {
                    return 'Nein';
                }
                break;

            case AppField::TYPE_RELATION:

                if(
                    isset($this->relations[$appField->getRelationClass()]) AND
                    $relation=$this->relations[$appField->getRelationClass()]
                ) {
                    /** @var EntityRepository $repository */
                    $repository=$this->relations[$appField->getRelationClass()]['repository'];

                    /** @var AbstractEntityInterface $entity */
                    $entity=$repository->find($appField->getData());

                    if($entity) {
                        $return=$entity->__toString();
                        if(
                            $html AND
                            $relation['route'] AND
                            $relation['route_id_param']
                        ) {
                            $link=$this->router->generate($relation['route'],[
                                $relation['route_id_param']=>$entity->getId(),
                            ]);
                            $return.=' <a href="'.$link.'"><i class="glyphicon glyphicon-link"></i></a>';
                        }
                        return $return;
                    }
                }

                return 'Unbekannte Relation '.$appField->getData();
                break;

        }

        return $appField->getData();
    }

}