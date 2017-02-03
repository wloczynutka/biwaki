<?php

namespace BiwakiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BiwakiBundle\Entity\Biwak;

/**
 * @ORM\Entity
 * @ORM\Table(name="biwaki_image")
 */
class Image
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Biwak", inversedBy="comments", cascade="persist")
     * @ORM\JoinColumn(name="biwak_id", referencedColumnName="id")
     */
    private $biwak;

    /**
     * @var string
     * @ORM\Column(name="text", type="text")
     */
    private $link;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Image
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBiwak()
    {
        return $this->biwak;
    }

    /**
     * @param mixed $biwak
     * @return Image
     */
    public function setBiwak(Biwak $biwak)
    {
        $this->biwak = $biwak;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return Image
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }


}