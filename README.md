# MultiAppBundle

## installation

Use `composer require creavo/multi-app-bundle` to install bundle.

Add the following to your config.yml (add to doctrine-section):

    doctrine:
        orm:
            dql:
                string_functions:
                    JSON_EXTRACT: Creavo\MultiAppBundle\Doctrine\JsonExtract
                    JSON_CONTAINS: Creavo\MultiAppBundle\Doctrine\JsonContains
                    
Add the following later on in your config.yml:

    creavo_multi_app:
        option: value

Add the following to your routing.yml:

    creavo_multi_app:
        resource: "@CreavoMultiAppBundle/Controller/"
        type:     annotation
        prefix:   /my-prefix
        
Add the following bundles to your AppKernel.php:

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                [...]
                new Creavo\MultiAppBundle\CreavoMultiAppBundle(),
                new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            ];
    
            
            return $bundles;
        }
        
        [...]
    }