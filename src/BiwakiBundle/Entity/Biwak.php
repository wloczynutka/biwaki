<?php

namespace BiwakiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use BiwakiBundle\Entity\User;

/**
 * Place
 *
 * @ORM\Table(name="biwaki_biwak")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Biwak
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
     * @var integer
     * @ORM\Column(name="source", type="integer")
     */
    private $source;

    /**
     * @var string
     * @ORM\Column(name="origin_id", type="string", nullable=true)
     */
    private $originId;

    /**
     * @ORM\OneToMany(targetEntity="Description", mappedBy="biwakId")
     */
    private $descriptions;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="biwak")
     */
    private $images;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", length=2, nullable=true)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="street", type="string", length=50, nullable=true)
     */
    private $street;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="BiwakiBundle\Entity\User", inversedBy="biwaks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var BiwakType
     * @ORM\OneToOne(targetEntity="BiwakType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="linkToOriginal", type="string", length=255, nullable=true)
     */
    private $linkToOriginal;

    /**
     * @var float
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var float
     * @ORM\Column(name="altitude", type="float")
     */
    private $altitude;

    /**
     * @var string
     * @ORM\Column(name="region", type="string", length=4, nullable=true)
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="biwakId")
     */
    private $attributes;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="biwakId")
     */
    private $comments;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime $lastUpdate
     * @ORM\Column(name="$last_update", type="datetime", nullable=false)
     */
    private $lastUpdate;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->descriptions = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set source
     * @param integer $source
     * @return Biwak
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     * @return integer
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set country
     * @param string $country
     * @return Biwak
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Biwak
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * Get street
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Biwak
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set username
     * @param string $username
     * @return Biwak
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set type
     * @param BiwakType $type
     * @return Biwak
     */
    public function setType(BiwakType $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set linkToOriginal
     * @param string $linkToOriginal
     * @return Biwak
     */
    public function setLinkToOriginal($linkToOriginal)
    {
        $this->linkToOriginal = $linkToOriginal;
        return $this;
    }

    /**
     * Get linkToOriginal
     * @return string
     */
    public function getLinkToOriginal()
    {
        return $this->linkToOriginal;
    }

    /**
     * Set latitude
     * @param float $latitude
     * @return Biwak
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Get latitude
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     * @param float $longitude
     * @return Biwak
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Biwak
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateCreated
     * @param \DateTime $dateCreated
     * @return Biwak
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Add descriptions
     * @param Description $description
     * @return Biwak
     */
    public function addDescription(Description $description)
    {
        $this->descriptions[] = $description;
        return $this;
    }

    /**
     * Remove descriptions
     * @param Description $description
     */
    public function removeDescription(Description $description)
    {
        $this->descriptions->removeElement($description);
    }

    /**
     * Get descriptions
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * Add descriptions
     * @param Attribute $attribute
     * @return GeoCache
     */
    public function addAttribute (Attribute $attribute)
    {
        $this->attributes[] = $attribute;
        return $this;
    }

    /**
     * Remove attribute
     * @param Attribute $attribute
     */
    public function removeAttribute(Attribute $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * Get attribute
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function getOriginId()
    {
        return $this->originId;
    }

    /**
     * @param int $originId
     * @return Biwak
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;
        return $this;
    }

    /**
     * Add descriptions
     * @param Comment $comment
     * @return Biwak
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * Remove descriptions
     * @param Comment $comment
     */
    public function removeComments(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get descriptions
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function getAltitude()
    {
        return $this->altitude;
    }

    public function setAltitude($altitude)
    {
        $this->altitude = $altitude;
        return $this;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTime $lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }


    /**
     * Add image
     * @param Image $image
     * @return Biwak
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;
        return $this;
    }

    /**
     * Remove image
     * @param Image $image
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if($this->dateCreated === null){
            $this->dateCreated = new \DateTime();
        }
        $this->lastUpdate = new \DateTime();
    }


}
