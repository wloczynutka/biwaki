<?php

namespace BiwakiBundle\Util\Core;

use BiwakiBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Filters to be used while query database
 *
 * @author Łza
 */
class BiwakiFilters
{
    private $owners;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
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
