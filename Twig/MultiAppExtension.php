<?php

namespace Creavo\MultiAppBundle\Twig;

use Creavo\MultiAppBundle\Classes\AppField;
use Creavo\MultiAppBundle\Helper\FormatHelper;

class MultiAppExtension extends \Twig_Extension
{

    protected $formatHelper;

    public function __construct(FormatHelper $formatHelper) {
        $this->formatHelper=$formatHelper;
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('crv_ma_format',function(AppField $appField, $default=null) {
                return $this->formatHelper->renderAppFieldData($appField,$default);
            },['is_safe'=>['html']]),
        ];
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('crv_ma_format',function(AppField $appField, $default=null) {
                return $this->formatHelper->renderAppFieldData($appField,$default);
            },['is_safe'=>['html']]),
        ];
    }


}
