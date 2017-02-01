<?php

namespace BiwakiBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ ORM\OneToMany(targetEntity="CarBundle\Entity\Car", mappedBy="user")
     */
//    protected $cars;


    public function __construct()
    {
//        $this->cars = new ArrayCollection();
        parent::__construct();
    }



}
