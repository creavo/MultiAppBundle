<?php

namespace Creavo\MultiAppBundle\Classes\Filters;

use Creavo\MultiAppBundle\Classes\AppField;

class AbstractFilter {

    /** @var AppField */
    protected $appField;

    /** @var string */
    protected $fieldSlug;

    /** @var mixed */
    protected $value;

    public function __construct(AppField $appField, $value=null) {
        $this->setAppField($appField);
        $this->setFieldSlug($appField->getSlug());
        $this->setValue($appField->getData());

        if($value) {
            $this->setValue($value);
        }
    }

    public function getParameterName($fieldSlug,$key=null) {
        if($key) {
            return $fieldSlug.'_'.$this->getName().'_'.mt_rand(1000,9999).'_'.$key;
        }
        return $fieldSlug.'_'.$this->getName().'_'.mt_rand(1000,9999);
    }

    public function getName() {
        throw new \LogicException('name must be overridden');
    }

    public function getValue(){
        return $this->value;
    }

    public function setValue($value){
        $this->value = $value;
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