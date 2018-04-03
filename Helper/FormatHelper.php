<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Classes\AppField;

class FormatHelper {

    public function __construct() {

    }

    public function renderAppFieldData(AppField $appField, $default=null) {

        $type=$appField->getType();

        if($appField->getData()===null) {
            return $default;
        }

        switch($type) {
            case AppField::TYPE_BOOLEAN:
                if($appField->getData()===true) {
                    return 'Ja';
                }
                if($appField->getData()===false) {
                    return 'Nein';
                }
                break;
        }

        return $appField->getData();
    }

}