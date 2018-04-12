<?php

namespace Creavo\MultiAppBundle\Interfaces;

use Doctrine\ORM\QueryBuilder;

interface UserRepositoryInterface {

    /**
     * find user-entity (which implements UserInterface) by given id
     *
     * @param $userId
     * @return UserInterface
     */
    public function find($userId);

    /**
     * create query-builder
     *
     * @param $alias
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias);

}