<?php

namespace Creavo\MultiAppBundle\Form\Type;

use Creavo\MultiAppBundle\Entity\App;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AppBasicType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('name',TextType::class,[
            'label'=>'App-Name',
            'required'=>true,
            'constraints'=>[
                new Length([
                    'min'=>4,
                    'max'=>255,
                ]),
            ],
        ]);

        $builder->add('itemSingularName',TextType::class,[
            'label'=>'Elementname - Einzahl',
            'required'=>true,
            'constraints'=>[
                new Length([
                    'min'=>4,
                    'max'=>64,
                ]),
            ],
        ]);

        $builder->add('itemPluralName',TextType::class,[
            'label'=>'Elementname - Mehrzahl',
            'required'=>true,
            'constraints'=>[
                new Length([
                    'min'=>4,
                    'max'=>64,
                ]),
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class'=>App::class,
        ]);
    }

}