<?php

namespace BiwakiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BiwakType
 *
 * @ORM\Table(name="biwaki_biwak_type")
 * @ORM\Entity
 */
class BiwakType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="text")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="icon_url", type="text")
     */
    private $iconUrl;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BiwakType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set biwak
     *
     * @param \stdClass $biwak
     *
     * @return BiwakType
     */
    public function setBiwak($biwak)
    {
        $this->biwak = $biwak;

        return $this;
    }

    /**
     * Get biwak
     *
     * @return \stdClass
     */
    public function getBiwak()
    {
        return $this->biwak;
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * @param string $iconUrl
     * @return BiwakType
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }



}

