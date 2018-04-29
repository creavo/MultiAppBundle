<?php

namespace Creavo\MultiAppBundle\Classes\Filters;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\ORM\QueryBuilder;

class LessThanEqualFilter extends AbstractFilter implements FilterInterface {

    public function getName() {
        return 'less_than_equal';
    }

    public static function getTypes() {
        return [
            AppField::TYPE_NUMBER,
            AppField::TYPE_MONEY,
            AppField::TYPE_PROGRESS,
        ];
    }

    public function filter(QueryBuilder $qb) {
        $filterName=$this->getParameterName($this->getFieldSlug());
        $qb->setParameter($filterName,(int)$this->getValue1());
        return $qb->expr()->lte("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."'))",':'.$filterName);
    }

    public function toText() {
        return $this->getAppField()->getName().' kleiner/gleich als "'.$this->getValue1().'"';
    }

}