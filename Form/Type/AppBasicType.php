<?php

namespace Creavo\MultiAppBundle\Form\Type;

use Creavo\MultiAppBundle\Classes\AppIcon;
use Creavo\MultiAppBundle\Entity\App;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class AppBasicType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        /** @var App $app */
        $app=$builder->getData();

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

        $builder->add('icon',ChoiceType::class,[
            'label'=>'Symbol',
            'required'=>true,
            'constraints'=>[
                new NotNull(),
            ],
            'choice_loader'=>new CallbackChoiceLoader(function() {
                return AppIcon::getChoicesAsObjects();
            }),
            'expanded'=>true,
            'choice_label'=>function(AppIcon $appIcon) {
                return '<i class="'.$appIcon->getCode().'" style="font-size:28px;"></i>';
            },
            'choice_value'=>function(AppIcon $appIcon=null) {
                return $appIcon ? $appIcon->getId() : '';
            },
        ]);

        $builder->get('icon')
            ->addModelTransformer(new CallbackTransformer(function($data) {
                return AppIcon::createById($data);
            },function(AppIcon $appIcon) {
                return $appIcon->getId();
            }));

    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class'=>App::class,
        ]);
    }

}