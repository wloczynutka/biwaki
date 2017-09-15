<?php

namespace BiwakiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute
 *
 * @ORM\Table(name="biwaki_attribute")
 * @ORM\Entity
 */
class Attribute
{

    const TYPE_ACT_WINDSURFING = 1;
    const TYPE_ACT_MTB = 2;
    const TYPE_ACT_HIKE = 3;
    const TYPE_ACT_KAYAK= 4;
    const TYPE_ACT_FISHING = 5;
    const TYPE_ACT_MOTO = 6;
    const TYPE_PETS = 7;
    const TYPE_TRASH_BINS = 8;
    const TYPE_WATER = 9;
    const TYPE_ELECTRICITY = 10;
    const TYPE_WASTE_WATER = 11;
    const TYPE_BEACH = 12;
    const TYPE_TOILETS = 13;
    const TYPE_SHOWER = 14;
    const TYPE_BAKERY = 15;
    const TYPE_WIFI = 16;
    const TYPE_SWIMMING_POOL = 17;
    const TYPE_WASHING = 18;
    const TYPE_CLIMBING = 19;

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Biwak", inversedBy="attributes")
     * @ORM\JoinColumn(name="biwak_id", referencedColumnName="id")
     */
    private $biwak;


    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     * @param integer $type
     * @return Attribute
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getBiwak()
    {
        return $this->biwak;
    }

    /**
     * @param Biwak $biwak\
     * @return Biwak
     */
    public function setBiwak(Biwak $biwak)
    {
        $this->biwak = $biwak;
        return $this;
    }

    
    
}
