# MultiAppBundle

## installation

Use `composer require creavo/multi-app-bundle` to install bundle.

Add the following to your config.yml:

    doctrine:
        orm:
            dql:
                string_functions:
                    JSON_EXTRACT: Creavo\MultiAppBundle\Doctrine\JsonExtractFunction
