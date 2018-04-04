<?php

namespace Creavo\MultiAppBundle\Form\Type;

use Creavo\MultiAppBundle\Classes\AppField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Url;

class ItemType extends AbstractType {

    /** @var array */
    protected $appFields;

    /** @var FormBuilderInterface */
    protected $builder;

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $data=$builder->getData();
        $this->appFields=$options['appFields'];
        $this->builder=$builder;

        /** @var AppField $appField */
        foreach($this->appFields AS $appField) {

            switch ($appField->getType()) {
                case AppField::TYPE_STRING:
                    $this->buildTextForm($appField);
                    break;

                case AppField::TYPE_CHOICE:
                    $this->buildChoiceForm($appField);
                    break;

                case AppField::TYPE_NUMBER:
                    $this->buildNumberForm($appField);
                    break;

                case AppField::TYPE_MONEY:
                    $this->buildMoneyForm($appField);
                    break;

                case AppField::TYPE_URL:
                    $this->buildUrlForm($appField);
                    break;

                case AppField::TYPE_EMAIL:
                    $this->buildEmailForm($appField);
                    break;

                case AppField::TYPE_PROGRESS:
                    $this->buildProgressForm($appField);
                    break;

                case AppField::TYPE_DATETIME:

                    break;

                case AppField::TYPE_BOOLEAN:
                    $this->buildBooleanForm($appField);
                    break;
            }


        }

    }

    protected function buildBooleanForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotNull(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        $this->builder->add($appField->getSlug(),ChoiceType::class,[
            'required'=>$required,
            'label'=>$label,
            'constraints'=>$constraints,
            'choices'=>[
                'Ja'=>true,
                'Nein'=>false,
            ],
            'attr'=>[
                'help'=>$appField->getHelpText(),
            ],
        ]);
    }

    protected function buildChoiceForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotNull(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        $this->builder->add($appField->getSlug(),ChoiceType::class,[
            'required'=>$required,
            'placeholder'=>'- bitte wählen -',
            'label'=>$label,
            'constraints'=>$constraints,
            'choices'=>array_flip($appField->getChoices()),
            'attr'=>[
                'help'=>$appField->getHelpText(),
            ],
        ]);
    }

    protected function buildProgressForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotNull(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        $this->builder->add($appField->getSlug(),PercentType::class,[
            'required'=>$required,
            'label'=>$label,
            'constraints'=>$constraints,
            'scale'=>0,
            'type'=>'integer',
            'attr'=>[
                'help'=>$appField->getHelpText(),
            ],
        ]);
    }

    protected function buildEmailForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[
            new Email(['message'=>'Es muss eine gültige E-Mail-Adresse eingegeben werden.']),
        ];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotBlank(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        $this->builder->add($appField->getSlug(),EmailType::class,[
            'required'=>$required,
            'label'=>$label,
            'constraints'=>$constraints,
            'attr'=>[
                'help'=>$appField->getHelpText(),
            ],
        ]);
    }

    protected function buildUrlForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[
            new Url(['message'=>'Es muss eine gültige URL eingegeben werden.']),
        ];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotBlank(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        $this->builder->add($appField->getSlug(),UrlType::class,[
            'required'=>$required,
            'label'=>$label,
            'constraints'=>$constraints,
            'attr'=>[
                'help'=>$appField->getHelpText(),
            ],
        ]);
    }

    protected function buildMoneyForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotBlank(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        $this->builder->add($appField->getSlug(),MoneyType::class,[
            'required'=>$required,
            'label'=>$label,
            'constraints'=>$constraints,
            'scale'=>$appField->getScale(),
            'currency'=>$appField->getCurrency(),
            'attr'=>[
                'help'=>$appField->getHelpText(),
            ],
        ]);
    }

    protected function buildNumberForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotBlank(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        $this->builder->add($appField->getSlug(),NumberType::class,[
            'required'=>$required,
            'label'=>$label,
            'constraints'=>$constraints,
            'scale'=>$appField->getScale(),
            'attr'=>[
                'help'=>$appField->getHelpText(),
            ],
        ]);
    }

    protected function buildTextForm(AppField $appField) {

        $required=false;
        $label=$appField->getName();
        $constraints=[];

        if($appField->getRequired()) {
            $required=true;
            $label.='*';
            $constraints[]=new NotBlank(['message'=>'Dieses Feld muss gefüllt werden.']);
        }

        if($appField->getTextArea()) {
            $this->builder->add($appField->getSlug(),TextareaType::class,[
                'required'=>$required,
                'label'=>$label,
                'constraints'=>$constraints,
                'attr'=>[
                    'help'=>$appField->getHelpText(),
                ],
            ]);
        }else{
            $this->builder->add($appField->getSlug(),TextType::class,[
                'required'=>$required,
                'label'=>$label,
                'constraints'=>$constraints,
                'attr'=>[
                    'help'=>$appField->getHelpText(),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'appFields'=>[],
        ]);
    }

}