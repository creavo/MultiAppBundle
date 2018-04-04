<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Classes\AppField;

class Normalizer {

    /** @var array */
    protected $appFields;

    public function __construct(array $appFields) {
        $this->appFields=$appFields;
    }

    public function transformDataToDatabase(array $data) {

        /** @var AppField $appField */
        foreach($this->appFields AS $appField) {

            if(
                !isset($data[$appField->getSlug()]) OR
                $data[$appField->getSlug()]===null
            ) {
                continue;
            }

            $value=$data[$appField->getSlug()];

            switch($appField->getType()) {
                case AppField::TYPE_DATETIME:
                    if($value instanceof \DateTime) {
                        $data[$appField->getSlug()]=$value->format('Y-m-d H:i:s');
                    }
                    break;
            }
        }

        return $data;
    }

    public function transformDataToPhp(array $data) {

        /** @var AppField $appField */
        foreach($this->appFields AS $appField) {

            if(
                !isset($data[$appField->getSlug()]) OR
                $data[$appField->getSlug()]===null
            ) {
                continue;
            }

            $value=$data[$appField->getSlug()];

            switch($appField->getType()) {
                case AppField::TYPE_DATETIME:
                    $data[$appField->getSlug()]=\DateTime::createFromFormat('Y-m-d H:i:s',$value);
                    break;
            }
        }

        return $data;
    }

}