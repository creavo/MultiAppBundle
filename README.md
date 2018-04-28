# MultiAppBundle

This bundle aims to create a light-wight platform to create lists/tables (here named apps) based on dynamically fields (like you can add a text-field to a table on the fly). In some functions its similar to podio. It also supports relations to other doctrine-entities you may already be using (see relations in config.xml).

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
                    JSON_SEARCH: Creavo\MultiAppBundle\Doctrine\JsonSearch
                    JSON_QUOTE: Creavo\MultiAppBundle\Doctrine\JsonQuote
                    JSON_UNQUOTE: Creavo\MultiAppBundle\Doctrine\JsonUnquote
                    
Add and edit the following to your needs later on in your config.yml:

    creavo_multi_app:
        user_class: AppBundle\Entity\User # user-class - must implement Creavo\MultiAppBundle\Interfaces\UserInterface
        relations: # relations to existing doctrine-entities (entity must implement Creavo\MultiAppBundle\Interfaces\AbstractEntityInterface)
            -
                name: User # name of entity - used in app-fields
                class: AppBundle\Entity\User # class of the entity
                route: user_detail # if a link to the entity is needed - this is the route
                route_id_param: id # and this the id-key - thats equals path('user_detail',{'id': entityId}) in twig
            -
                name: Customer
                class: AppBundle\Entity\Customer
        

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