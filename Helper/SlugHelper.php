<?php

namespace Creavo\MultiAppBundle\Helper;

use Creavo\MultiAppBundle\Entity\App;
use Creavo\MultiAppBundle\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Registry;

class SlugHelper {

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    public function __construct(Registry $registry) {
        $this->em=$registry->getManager();
    }

    public function createSlugForWorkspace($name) {

    }

    public function createSlugForApp(Workspace $workspace, $name) {

    }

    public function createSlugForAppField(App $app, $fieldName, array $generatedSlugs=[]) {

    }

    public static function createSlugFromString($name, $separator='-') {
        $name=mb_strtolower($name);
        return preg_replace('/([^a-z0-9]+)/', $separator, self::removeAccents($name));
    }

    private static function removeAccents($string) {
        $replaces=[
            'ä'=>'ae',
            'ü'=>'ue',
            'ö'=>'oe',
            'ß'=>'ss',
        ];

        $originals=[];
        $replacements=[];

        foreach($replaces AS $original=>$replacement) {
            $originals[]=$original;
            $replacements[]=$replacement;
        }

        $string=str_replace($originals,$replacements,$string);
        return $string;
    }
}