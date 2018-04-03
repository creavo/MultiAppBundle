<?php

namespace Creavo\MultiAppBundle\Classes;

class AppField {

    const TYPE_STRING=1;
    const TYPE_CHOICE=2;
    const TYPE_NUMBER=3;
    const TYPE_MONEY=4;
    const TYPE_URL=5;
    const TYPE_EMAIL=6;
    const TYPE_PROGRESS=7;
    const TYPE_DATETIME=8;
    const TYPE_BOOLEAN=9;

    const TYPES=[
        'Text'=>self::TYPE_STRING,
        'Auswahl'=>self::TYPE_CHOICE,
        'Zahl'=>self::TYPE_NUMBER,
        'Währungsbetrag'=>self::TYPE_MONEY,
        'URL'=>self::TYPE_URL,
        'E-Mail'=>self::TYPE_EMAIL,
        'Fortschritt'=>self::TYPE_PROGRESS,
        'Zeitpunkt'=>self::TYPE_DATETIME,
        'Ja/Nein'=>self::TYPE_BOOLEAN,
    ];

    protected $name;
    protected $type;
    protected $slug;
    protected $required=false;
    protected $showListing=true;
    protected $hideWhenEmpty=false;
    protected $textArea=false;
    protected $scale=10;
    protected $precision=2;
    protected $currency='EUR';
    protected $data;

    public function __construct($type,$name,$slug=null) {

        $this->setType($type);
        $this->setName($name);
        if($slug) {
            $this->setSlug($slug);
        }
    }

    public function getTableClass() {

        if(in_array($this->getType(),[
            self::TYPE_NUMBER,
            self::TYPE_MONEY,
        ],false)) {
            return 'text-right';
        }

        return '';
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
        return $this;
    }

    public function isRequired(){
        return $this->required;
    }

    public function getRequired(){
        return $this->required;
    }

    public function setRequired($required){
        $this->required = $required;
        return $this;
    }

    public function isShowListing(){
        return $this->showListing;
    }

    public function getShowListing(){
        return $this->showListing;
    }

    public function setShowListing($showListing){
        $this->showListing = $showListing;
        return $this;
    }

    public function isHideWhenEmpty(){
        return $this->hideWhenEmpty;
    }

    public function getHideWhenEmpty(){
        return $this->hideWhenEmpty;
    }

    public function setHideWhenEmpty($hideWhenEmpty){
        $this->hideWhenEmpty = $hideWhenEmpty;
        return $this;
    }

    public function isTextArea(){
        return $this->textArea;
    }

    public function getTextArea(){
        return $this->textArea;
    }

    public function setTextArea($textArea){
        $this->textArea = $textArea;
        return $this;
    }

    public function getScale(){
        return $this->scale;
    }

    public function setScale($scale){
        $this->scale = $scale;
        return $this;
    }

    public function getPrecision(){
        return $this->precision;
    }

    public function setPrecision($precision){
        $this->precision = $precision;
        return $this;
    }

    public function getData(){
        return $this->data;
    }

    public function setData($data){
        $this->data = $data;
        return $this;
    }

    public function getSlug(){
        return $this->slug;
    }

    public function setSlug($slug){
        $this->slug = $slug;
        return $this;
    }

    public function getCurrency(){
        return $this->currency;
    }

    public function setCurrency($currency){
        $this->currency = $currency;
        return $this;
    }



}