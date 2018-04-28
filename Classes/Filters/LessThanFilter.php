<?php

namespace Creavo\MultiAppBundle\Classes\Filters;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\ORM\QueryBuilder;

class LessThanFilter extends AbstractFilter implements FilterInterface {

    public function getName() {
        return 'less_than';
    }

    public function getTypes() {
        return [
            AppField::TYPE_NUMBER,
            AppField::TYPE_MONEY,
            AppField::TYPE_PROGRESS,
        ];
    }

    public function filter(QueryBuilder $qb) {

        $filterName=$this->getParameterName($this->getFieldSlug());
        $qb
            ->andWhere("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."')) < :".$filterName)
            ->setParameter($filterName,'%'.$this->getValue().'%');
    }

    public function toText() {
        return $this->getAppField()->getName().' kleiner als "'.$this->getValue().'"';
    }

}