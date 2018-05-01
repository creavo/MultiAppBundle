<?php

namespace Creavo\MultiAppBundle\Twig;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Helper\FormatHelper;
use Creavo\MultiAppBundle\Helper\ItemHelper;

class MultiAppExtension extends \Twig_Extension
{

    /** @var FormatHelper */
    protected $formatHelper;

    /** @var ItemHelper */
    protected $itemHelper;

    public function __construct(FormatHelper $formatHelper, ItemHelper $itemHelper) {
        $this->formatHelper=$formatHelper;
        $this->itemHelper=$itemHelper;
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('crv_ma_format',function(AppField $appField, $default=null) {
                return $this->formatHelper->renderAppFieldData($appField,$default);
            },['is_safe'=>['html']]),
            new \Twig_SimpleFunction('crv_ma_user',function($userId, $default=null) {
                if($user=$this->itemHelper->getUserById($userId)) {
                    return $user->__toString();
                }
                return $default;
            }),
        ];
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('crv_ma_format',function(AppField $appField, $default=null) {
                return $this->formatHelper->renderAppFieldData($appField,$default);
            },['is_safe'=>['html']]),
            new \Twig_SimpleFilter('crv_ma_user',function($userId, $default=null) {
                if($user=$this->itemHelper->getUserById($userId)) {
                    return $user->__toString();
                }
                return $default;
            }),
            new \Twig_SimpleFilter('crv_ma_boolean',function($value) {
                if($value===true OR $value==='y') {
                    return 'Ja';
                }
                if($value===false OR $value==='n') {
                    return 'Nein';
                }
                return null;
            }),
        ];
    }


}
