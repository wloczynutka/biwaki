<?php

namespace BiwakiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BiwakiBundle\Entity\Biwak;

/**
 * @ORM\Entity
 * @ORM\Table(name="biwaki_description")
 */
class Description
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Biwak", inversedBy="comments")
     * @ORM\JoinColumn(name="biwak_id", referencedColumnName="id")
     */
    private $biwakId;

    /**
     * @var string
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", length=2)
     */
    private $language;

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * set BiwakId
     * @param Biwak
     * @return Comment
     */
    public function setBiwakId(Biwak $biwak = null)
    {
        $this->biwakId = $biwak;
        return $this;
    }

    /**
     * Get BiwakId
     * @return Biwak
     */
    public function getBiwakId()
    {
        return $this->biwakId;
    }

     /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return Description
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }


}
