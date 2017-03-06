<?php

namespace BiwakiBundle\Util\Core;

use BiwakiBundle\Entity\User;
/**
 * Description of BiwakiFilters
 *
 * @author Łza
 */
class BiwakiFilters
{
    private $owners;

    public function __construct()
    {
        $this->owners = new ArrayObject();
    }

    public function getOwner()
    {
        return $this->owners;
    }

    public function addOwner(User $owner)
    {
        $this->owners[] = $owner;
        return $this;
    }



}
