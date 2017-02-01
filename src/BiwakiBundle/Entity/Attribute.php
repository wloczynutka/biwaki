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
     * @ORM\ManyToOne(targetEntity="Biwak", inversedBy="comments")
     * @ORM\JoinColumn(name="biwak_id", referencedColumnName="id")
     */
    private $biwakId;


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

}
