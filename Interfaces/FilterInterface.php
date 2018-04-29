<?php

namespace Creavo\MultiAppBundle\Interfaces;

use Creavo\MultiAppBundle\Classes\AppField;
use Doctrine\ORM\QueryBuilder;

interface FilterInterface {


    public function __construct(AppField $appField,$value=null);

    public function getName();

    public static function getTypes();

    public function filter(QueryBuilder $qb);

    public function toText();

    public function setFieldSlug($fieldSlug);
    public function getFieldSlug();
    public function setValue1($value1);
    public function getValue1();
    public function setValue2($value2);
    public function getValue2();

    public function getValue1FormType();
    public function getValue2FormType();
    public function getValue1FormOptions();
    public function getValue2FormOptions();

}