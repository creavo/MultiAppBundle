# MultiAppBundle

This bundle aims to create a light-wight platform to create lists/tables (here named apps) based on dynamically fields (like you can add a text-field to a table on the fly). In some functions its similar to podio.

## Status: alpha - do not use! :)

## requirements

* symfony-instance (version 3.3, maybe older is possible, haven't testet it yet)
* MySQL-database with JSON-functions (like json_contains or json_extract - available in mysql 5.7 or maria-db 10.2)

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
    
## styling

This bundle brings a basic ui, based on bootstrap 3.3. Overwrite the base `@CreavoMultiApp/base.html.twig` to match the markers you use in your templates.
    
## usage

Create new item:

    $this->get('crv.ma.item')->createItem($myApp,['field1'=>'value1','field2'=>'value2']);
    
Update item (creates a new revision in the background):

    $this->get('crv.ma.item')->updateItem($myItem,['field1'=>'value0815','field2'=>'value4711']);
    
Get item-data with app-fields

    $this->get('crv.ma.item')->getItemRow($myItem);