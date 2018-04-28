<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Classes\Filters\ContainsFilter;
use Creavo\MultiAppBundle\Classes\Filters\StartsWithFilter;
use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class FilterHelper {

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    public function __construct(Registry $registry) {
        $this->em=$registry->getManager();
    }

    public function getFilterTexts(App $app, Request $request) {
        $texts=[];

        /** @var FilterInterface $filter */
        foreach($this->getFilters($app,$request) AS $filter) {
            $texts[]=$filter->toText();
        }

        return $texts;
    }

    public function modifyQueryBuilder(App $app, Request $request, QueryBuilder $queryBuilder) {

        /** @var FilterInterface $filter */
        foreach($this->getFilters($app,$request) AS $filter) {
            $filter->filter($queryBuilder);
        }

    }

    protected function getFilters(App $app, Request $request) {
        $data=[];

        /** @var AppField $appField */
        foreach($app->getAppFieldsFromApp() AS $appField) {

            if($appField->getType()===AppField::TYPE_STRING) {
                //$filter=new ContainsFilter($appField,'e');
                $filter=new StartsWithFilter($appField,'a');
                //$data[]=$filter;
                break;
            }

        }

        return $data;
    }

}