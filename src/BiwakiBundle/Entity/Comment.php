<?php

namespace BiwakiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BiwakiBundle\Entity\Biwak;

/**
 * @ORM\Entity
 * @ORM\Table(name="biwaki_comment")
 */
class Comment
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
     * @ORM\Column(type="datetime", name="date_created")
     */
    private $dateCreated;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=50)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(name="text", type="text")
     */
    private $text;


    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * Set setBiwakId
     * @param Biwak
     * @return Comment
     */
    public function setBiwakId(Biwak $biwak)
    {
        $this->biwakId = $biwak;
        return $this;
    }

    /**
     * Get biwakId
     *
     * @return Biwak
     */
    public function getBiwakId()
    {
        return $this->biwakId;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Comment
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return Comment
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
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


}
