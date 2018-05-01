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
    const TYPE_RELATION=10;

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
        'Relation'=>self::TYPE_RELATION,
    ];

    protected $name;
    protected $type;
    protected $helpText;
    protected $slug;
    protected $required=false;
    protected $showListing=true;
    protected $textArea=false;
    protected $withTime=false;
    protected $scale=2;
    protected $currency='EUR';
    protected $choices=[];
    protected $relationClass;
    protected $data;

    public function __construct($type,$name,$slug=null) {

        $this->setType($type);
        $this->setName($name);
        if($slug) {
            $this->setSlug($slug);
        }
    }

    public function hasSecondFilterValue() {

        if(in_array($this->getType(),[
            self::TYPE_NUMBER,
            self::TYPE_MONEY,
            self::TYPE_PROGRESS,
            self::TYPE_DATETIME,
        ],false)) {
            return true;
        }

        return false;
    }

    public function getAlign() {

        if(in_array($this->getType(),[
            self::TYPE_NUMBER,
            self::TYPE_MONEY,
        ],false)) {
            return 'right';
        }

        return 'left';
    }

    public function getTypeName() {
        if($text=\array_search($this->getType(),self::TYPES,false)) {
            return $text;
        }
        return null;
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

    public function getChoices(){
        return $this->choices;
    }

    public function setChoices($choices){
        $this->choices = $choices;
        return $this;
    }

    public function getHelpText(){
        return $this->helpText;
    }

    public function setHelpText($helpText){
        $this->helpText = $helpText;
        return $this;
    }

    public function getRelationClass(){
        return $this->relationClass;
    }

    public function setRelationClass($relationClass){
        $this->relationClass = $relationClass;
        return $this;
    }

    public function isWithTime(){
        return $this->withTime;
    }

    public function setWithTime($withTime){
        $this->withTime = $withTime;
        return $this;
    }

}