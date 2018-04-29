<?php

namespace Creavo\MultiAppBundle\Classes\Filters;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Interfaces\FilterInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class NumberRangeFilter extends AbstractFilter implements FilterInterface {

    public function getName() {
        return 'number_range';
    }

    public static function getTypes() {
        return [
            AppField::TYPE_NUMBER,
            AppField::TYPE_MONEY,
            AppField::TYPE_PROGRESS,
        ];
    }

    public function getValue1FormType(){
        return IntegerType::class;
    }

    public function getValue2FormType(){
        return IntegerType::class;
    }

    public function getValue1FormOptions() {
        return [
            'label'=>'Wert 1',
            'required'=>false,
        ];
    }

    public function getValue2FormOptions() {
        return [
            'label'=>'Wert 2',
            'required'=>false,
        ];
    }

    public function filter(QueryBuilder $qb) {
        $filterName1=$this->getParameterName($this->getFieldSlug()).'_1';
        $filterName2=$this->getParameterName($this->getFieldSlug()).'_2';

        if($this->getValue1() AND $this->getValue2()) {
            $qb->setParameter($filterName1,(int)$this->getValue1());
            $qb->setParameter($filterName2,(int)$this->getValue2());
            return $qb->expr()->andX(
                $qb->expr()->gte("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."'))",':'.$filterName1),
                $qb->expr()->lte("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."'))",':'.$filterName2)
            );
        }

        if($this->getValue1()) {
            $qb->setParameter($filterName1,(int)$this->getValue1());
            return $qb->expr()->gte("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."'))",':'.$filterName1);
        }

        if($this->getValue2()) {
            $qb->setParameter($filterName2,(int)$this->getValue2());
            return $qb->expr()->lte("JSON_UNQUOTE(JSON_EXTRACT(ir.data,'$.".$this->getFieldSlug()."'))",':'.$filterName2);
        }

        return null;
    }

    public function toText() {
        if($this->getValue1() AND $this->getValue2()) {
            return $this->getAppField()->getName().' ist zwischen "'.$this->getValue1().'" und "'.$this->getValue2().'"';
        }
        if($this->getValue1()) {
            return $this->getAppField()->getName().' ist größer als "'.$this->getValue1().'"';
        }
        if($this->getValue2()) {
            return $this->getAppField()->getName().' ist kleiner als "'.$this->getValue2().'"';
        }

        return null;
    }

}