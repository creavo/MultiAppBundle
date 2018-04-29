<?php

namespace Creavo\MultiAppBundle\Classes\Filters;

use Creavo\MultiAppBundle\Classes\AppField;

class AbstractFilter {

    const FILTERS=[
        ContainsFilter::class,
        StartsWithFilter::class,
        EndsWithFilter::class,
        //GreaterThanFilter::class,
        //GreaterThanEqualFilter::class,
        //LessThanFilter::class,
        //LessThanEqualFilter::class,
        NumberRangeFilter::class,
        DateTimeRangeFilter::class,
    ];

    /** @var AppField */
    protected $appField;

    /** @var string */
    protected $fieldSlug;

    /** @var mixed */
    protected $value1;

    /** @var mixed */
    protected $value2;

    public function __construct(AppField $appField, $value1=null, $value2=null) {
        $this->setAppField($appField);
        $this->setFieldSlug($appField->getSlug());
        $this->setValue1($appField->getData());

        if($value1) {
            $this->setValue1($value1);
        }
        if($value2) {
            $this->setValue2($value2);
        }
    }

    public function getParameterName($fieldSlug,$key=null) {
        if($key) {
            return $fieldSlug.'_'.$this->getName().'_'.mt_rand(1000,9999).'_'.$key;
        }
        return $fieldSlug.'_'.$this->getName().'_'.mt_rand(1000,9999);
    }

    public function getValue1FormType() {
        return null;
    }

    public function getValue2FormType() {
        return null;
    }

    public function getValue1FormOptions() {
        return [
            'label'=>'Wert 1',
            'required'=>true,
        ];
    }

    public function getValue2FormOptions() {
        return [
            'label'=>'Wert 2',
            'required'=>true,
        ];
    }

    public function getName() {
        throw new \LogicException('name must be overridden');
    }

    public function getValue1(){
        return $this->value1;
    }

    public function setValue1($value1){
        $this->value1 = $value1;
        return $this;
    }

    public function getValue2(){
        return $this->value2;
    }

    public function setValue2($value2){
        $this->value2 = $value2;
        return $this;
    }

    public function getAppField(){
        return $this->appField;
    }

    public function setAppField(AppField $appField){
        $this->appField = $appField;
        return $this;
    }

    public function getFieldSlug(){
        return $this->fieldSlug;
    }

    public function setFieldSlug($fieldSlug){
        $this->fieldSlug = $fieldSlug;
        return $this;
    }


}