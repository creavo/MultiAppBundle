<?php

namespace Creavo\MultiAppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class CreavoMultiAppExtension extends Extension
{

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if(
            $config['user_class'] AND
            !in_array('Creavo\MultiAppBundle\Interfaces\UserInterface',class_implements($config['user_class']),false)
        ) {
            throw new LogicException('class '.$config['user_class'].' does not implement Creavo\MultiAppBundle\Interfaces\UserInterface');
        }

        $relations=[];
        foreach((array)$config['relations'] AS $relation) {
            if(!in_array('Creavo\MultiAppBundle\Interfaces\AbstractEntityInterface',class_implements($relation['class']))) {
                throw new LogicException('relation-class '.$relation['class'].' does not implement Creavo\MultiAppBundle\Interfaces\AbstractEntityInterface');
            }

            $relations[$relation['class']]=[
                'name'=>$relation['name'],
                'class'=>$relation['class'],
                'repository'=>null,
                'route'=>$relation['route'],
                'route_id_param'=>$relation['route_id_param'],
            ];
        }

        $itemHelperDefinition=$container->getDefinition('creavo_multi_app.helper.item_helper');
        $itemHelperDefinition->replaceArgument(1,$config['user_class']);
        $itemHelperDefinition->replaceArgument(2,$relations);

        $formatHelperDefinition=$container->getDefinition('creavo_multi_app.helper.format_helper');
        $formatHelperDefinition->replaceArgument(2,$relations);
    }
}
