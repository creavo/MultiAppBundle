<?php

namespace Creavo\MultiAppBundle\Classes\Filters;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\ORM\QueryBuilder;

class EndsWithFilter extends AbstractFilter implements FilterInterface {

    public function getName() {
        return 'ends_with';
    }

    public function getTypes() {
        return [
            AppField::TYPE_STRING,
        ];
    }

    public function filter(QueryBuilder $qb) {

        $filterName=$this->getParameterName($this->getFieldSlug());
        $qb
            ->andWhere("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."')) LIKE :".$filterName)
            ->setParameter($filterName,'%'.$this->getValue());
    }

    public function toText() {
        return $this->getAppField()->getName().' endet mit "'.$this->getValue().'"';
    }

}