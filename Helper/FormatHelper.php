<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Classes\AppField;

class FormatHelper {

    public function __construct() {

    }

    public function renderAppFieldData(AppField $appField, $default=null, $html=true) {

        $type=$appField->getType();

        if($appField->getData()===null) {
            return $default;
        }

        switch($type) {

            case AppField::TYPE_STRING:

                break;

            case AppField::TYPE_CHOICE:
                if(isset($appField->getChoices()[$appField->getData()])) {
                    return $appField->getChoices()[$appField->getData()];
                }
                return 'Unbekannte Option';
                break;

            case AppField::TYPE_NUMBER:
                return number_format($appField->getData(),$appField->getScale());
                break;

            case AppField::TYPE_MONEY:
                return number_format($appField->getData(),$appField->getScale()).' '.$appField->getCurrency();
                break;

            case AppField::TYPE_URL:
                if($html) {
                    return '<a href="'.$appField->getData().'" target="_blank">'.$appField->getData().'</a>';
                }
                break;

            case AppField::TYPE_EMAIL:
                if($html) {
                    return '<a href="mailto:'.$appField->getData().'">'.$appField->getData().'</a>';
                }
                break;

            case AppField::TYPE_PROGRESS:
                if($html) {
                    return '<div class="progress" style="margin-bottom:0;"><div class="progress-bar" style="min-width:2em;width:'.$appField->getData().'%;">'.$appField->getData().'%</div></div>';
                }
                return $appField->getData().'%';
                break;

            case AppField::TYPE_DATETIME:

                break;

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