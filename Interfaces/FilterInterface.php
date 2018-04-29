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
    public function setValue1($value);
    public function getValue1();

}