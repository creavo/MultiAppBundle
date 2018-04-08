<?php

namespace Creavo\MultiAppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActivityCommentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('comment',TextType::class,[
            'label'=>false,
            'required'=>true,
            'constraints'=>[
                new NotBlank(),
                new Length([
                    'max'=>2048,
                ])
            ]
        ]);

    }

    public function configureOptions(OptionsResolver $resolver) {

    }

}