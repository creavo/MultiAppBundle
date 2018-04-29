<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Classes\Filters\AbstractFilter;
use Creavo\MultiAppBundle\Classes\Filters\ContainsFilter;
use Creavo\MultiAppBundle\Classes\Filters\StartsWithFilter;
use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    public function getFilterObjects(App $app, Request $request) {
        return $this->getFilters($app,$request);
    }

    public function modifyQueryBuilder(App $app, Request $request, QueryBuilder $queryBuilder) {

        $sortedFilters=[];

        /** @var FilterInterface $filter */
        foreach($this->getFilters($app,$request) AS $filter) {
            $sortedFilters[$filter->getFieldSlug()][]=$filter;
        }

        foreach($sortedFilters AS $slug=>$sortedFilter) {

            $orX=$queryBuilder->expr()->orX();

            /** @var FilterInterface $filter */
            foreach((array)$sortedFilter AS $filter) {
                $orX->add($filter->filter($queryBuilder));
            }

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX($orX)
            );
        }

    }

    public function getPossibleFilters(App $app) {
        $data=[];

        /** @var AppField $appField */
        foreach((array)$app->getAppFieldsFromApp() as $appField) {

            $filters=[];

            foreach(AbstractFilter::FILTERS AS $filterClass) {
                $supports=call_user_func([$filterClass,'getTypes']);
                if(in_array($appField->getType(),$supports,false)) {
                    $filters[]=new $filterClass($appField,'[Wert 1]','[Wert 2]');
                }
            }

            $data[$appField->getSlug()]=[
                'appField'=>$appField,
                'filters'=>$filters,
            ];
        }

        return $data;
    }

    protected function getFilters(App $app, Request $request) {
        $data=[];

        $sessionData=$this->getSessionData($app,$request);
        $serializer=new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);

        foreach((array)$sessionData['filters'] AS $key=>$filter) {
            $data[$key]=$serializer->deserialize($filter['data'],$filter['class'],'json');
        }

        return $data;
    }

    public function removeFilter(App $app, Request $request, $filterKey) {
        $sessionData=$this->getSessionData($app,$request);
        if(isset($sessionData['filters'][$filterKey])) {
            unset($sessionData['filters'][$filterKey]);
        }
        $this->setSessionData($app,$request,$sessionData);
    }

    public function removeAllFilters(App $app, Request $request) {
        $sessionData=$this->getSessionData($app,$request);
        $sessionData['filters']=[];
        $this->setSessionData($app,$request,$sessionData);
    }

    public function addFilter(App $app, Request $request, FilterInterface $filter) {

        $serializer=new Serializer([new ObjectNormalizer()],[new JsonEncoder()]);

        $sessionData=$this->getSessionData($app,$request);
        $sessionData['filters'][]=[
            'data'=>$serializer->serialize($filter,'json'),
            'class'=>get_class($filter),
        ];
        $this->setSessionData($app,$request,$sessionData);
    }

    protected function getSessionData(App $app, Request $request) {

        /** @var SessionInterface $session */
        if($session=$request->getSession()) {

            if(!$session->has('crv_ma_item_filters')) {
                $session->set('crv_ma_item_filters',[]);
            }

            $slug=$app->getWorkspace()->getSlug().'_'.$app->getSlug();

            $sessionData=$session->get('crv_ma_item_filters');

            if(!isset($sessionData[$slug])) {
                $sessionData[$slug]=[
                    'filters'=>[],
                ];
                $session->set('crv_ma_item_filters',$sessionData);
            }

            return $sessionData[$slug];
        }

        return null;
    }

    protected function setSessionData(App $app, Request $request, array $data) {

        /** @var SessionInterface $session */
        if($session=$request->getSession()) {

            if(!$session->has('crv_ma_item_filters')) {
                $session->set('crv_ma_item_filters',[]);
            }

            $slug=$app->getWorkspace()->getSlug().'_'.$app->getSlug();

            $sessionData=$session->get('crv_ma_item_filters');
            $sessionData[$slug]=$data;
            $session->set('crv_ma_item_filters',$sessionData);

            return $sessionData[$slug];
        }

        return null;
    }
}