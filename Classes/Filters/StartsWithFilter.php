<?php

namespace Creavo\MultiAppBundle\Classes\Filters;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\ORM\QueryBuilder;

class StartsWithFilter extends AbstractFilter implements FilterInterface {

    public function getName() {
        return 'starts_with';
    }

    public static function getTypes() {
        return [
            AppField::TYPE_STRING,
            AppField::TYPE_URL,
            AppField::TYPE_EMAIL,
        ];
    }

    public function filter(QueryBuilder $qb) {
        $filterName=$this->getParameterName($this->getFieldSlug());
        $qb->setParameter($filterName,$this->getValue1().'%');
        return $qb->expr()->like("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."'))",':'.$filterName);
    }

    public function toText() {
        return $this->getAppField()->getName().' beginnt mit "'.$this->getValue1().'"';
    }

}