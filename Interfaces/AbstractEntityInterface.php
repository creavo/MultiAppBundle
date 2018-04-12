<?php

namespace Creavo\MultiAppBundle\Interfaces;

interface AbstractEntityInterface {

    /**
     * id of given entity
     *
     * @return int
     */
    public function getId();

    /**
     * name of given entity
     *
     * @return string
     */
    public function __toString();

}