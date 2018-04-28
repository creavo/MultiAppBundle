<?php

namespace Creavo\MultiAppBundle\Classes;

class AppIcon {

    const ICONS=[
        'glyphicon glyphicon-asterisk'=>1,
        'glyphicon glyphicon-cloud'=>2,
        'glyphicon glyphicon-envelope'=>3,
        'glyphicon glyphicon-music'=>4,
        'glyphicon glyphicon-heart'=>5,
        'glyphicon glyphicon-star'=>6,
        'glyphicon glyphicon-user'=>7,
        'glyphicon glyphicon-film'=>8,
        'glyphicon glyphicon-cog'=>9,
        'glyphicon glyphicon-road'=>10,
        'glyphicon glyphicon-time'=>11,
        'glyphicon glyphicon-stats'=>12,
        'glyphicon glyphicon-king'=>13,
        'glyphicon glyphicon-lamp'=>14,
        'glyphicon glyphicon-piggy-bank'=>15,
    ];

    protected $id;

    protected $code;

    public function __construct($id,$code) {
        $this->setId($id);
        $this->setCode($code);
    }

    public function __toString() {
        return ''.$this->getCode();
    }

    public static function getChoicesAsObjects() {
        $data=[];

        foreach(self::ICONS AS $code=>$id) {
            $data[$id]=new self($id,$code);
        }

        return $data;
    }

    public static function createById($id) {
        if($code=\array_search($id,self::ICONS,false)) {
            return new self($id,$code);
        }
        return null;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
        return $this;
    }

    public function getCode(){
        return $this->code;
    }

    public function setCode($code){
        $this->code = $code;
        return $this;
    }

}