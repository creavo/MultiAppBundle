<?php

namespace Creavo\MultiAppBundle\Interfaces;

interface UserInterface {

    /**
     * id of given user, saved in database
     *
     * @return int
     */
    public function getId();

    /**
     * name of the given user
     *
     * @return string
     */
    public function __toString();

    /**
     * full-path or base64-encoded inline-image to user-avatar / to be used in <img src="$path" />
     * return null if you don't have user-avatars
     *
     * @return string|null
     */
    public function getAvatar();

}