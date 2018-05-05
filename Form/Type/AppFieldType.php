<?php

namespace Creavo\MultiAppBundle\Form\Type;

use Creavo\MultiAppBundle\Classes\AppField;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class AppFieldType extends AbstractType {

    /** @var FormBuilderInterface */
    protected $builder;

    /** @var ObjectManager */
    protected $em;

    /** @var AppField */
    protected $appField;

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $this->em=$options['em'];
        $this->builder=$builder;
        $this->appField=$builder->getData();

        $this->builder->add('name',TextType::class,[
            'label'=>'Name',
            'required'=>true,
            'constraints'=>[
                new Length([
                    'min'=>4,
                    'max'=>255,
                ]),
                new NotBlank(),
            ],
        ]);

        $this->builder->add('required',CheckboxType::class,[
            'label'=>'Ist ein Pflichtfeld',
            'required'=>false,
        ]);

        $this->builder->add('showListing',CheckboxType::class,[
            'label'=>'Zeige in Liste',
            'required'=>false,
        ]);

        $this->builder->add('helpText',TextareaType::class,[
            'label'=>'Hilfetext',
            'required'=>false,
            'attr'=>[
                'help'=>'Wird bei Bearbeitung angezeigt (so wie hier)',
            ],
            'constraints'=>[
                new Length([
                    'max'=>2048,
                ]),
            ]
        ]);

        switch ($this->appField->getType()) {
            case AppField::TYPE_STRING:
                $this->buildTextForm();
                break;

            case AppField::TYPE_CHOICE:

                break;

            case AppField::TYPE_NUMBER:

                break;

            case AppField::TYPE_MONEY:
                $this->buildMoneyForm();
                break;

            case AppField::TYPE_URL:

                break;

            case AppField::TYPE_EMAIL:

                break;

            case AppField::TYPE_PROGRESS:

                break;

            case AppField::TYPE_DATETIME:
                $this->buildDateTimeForm();
                break;

            case AppField::TYPE_BOOLEAN:

                break;

            case AppField::TYPE_RELATION:
                $this->buildRelationType();
                break;
        }

    }

    protected function buildTextForm() {
        $this->builder->add('textArea',CheckboxType::class,[
            'label'=>'Mehrzeilige Texteingabe',
            'required'=>false,
        ]);
    }

    protected function buildMoneyForm() {

        $currencies=[];
        foreach(\Symfony\Component\Intl\Intl::getCurrencyBundle()->getCurrencyNames() AS $key=>$val) {
            $currencies[$key.' - '.$val]=$key;
        }

        $this->builder->add('currency',ChoiceType::class,[
            'label'=>'Währung',
            'placeholder'=>'- bitte wählen -',
            'required'=>true,
            'preferred_choices'=>[
                'EUR',
                'USD',
                'GBP',
            ],
            'constraints'=>[
                new NotNull(),
            ],
            'choices'=>$currencies,
        ]);
    }

    protected function buildDateTimeForm() {

    }

    protected function buildRelationType() {

    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class'=>AppField::class,
            'em'=>null,
        ]);
    }

}