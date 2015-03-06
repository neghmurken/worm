<?php

namespace Worm\SiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Worm\SiteBundle\Queue\Queue;

/**
 * @ORM\Entity(repositoryClass="Worm\SiteBundle\Entity\Repository\WormRepository")
 * @ORM\Table(name="ws_worm")
 * Class Worm
 * @package Worm\SiteBundle\Entity
 */
class Worm
{

    const MODE_HORIZONTAL = 1;
    const MODE_VERTICAL = 2;

    /**
     * @param bool $onlyKeys
     * @return array
     */
    public static function getModes($onlyKeys = false)
    {
        $modes = array(
            static::MODE_HORIZONTAL => 'Horizontal',
            static::MODE_VERTICAL => 'Vertical'
        );

        return true === $onlyKeys ? array_keys($modes) : $modes;
    }

    /**
     * @return Worm
     */
    public static function createDefault()
    {
        $worm = new static();
        $worm->setMode(static::MODE_HORIZONTAL);
        $worm->setWidth(640);
        $worm->setHeight(360);
        $worm->setTimeLimit(5 * 24 * 60);

        return $worm;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255, maxMessage="Le titre ne doit pas excéder 255 caractères")
     * @Assert\NotBlank(message="Vous devez définir un titre")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Assert\Length(max=2000, maxMessage="La description ne doit pas excéder 2000 caractères")
     * @var string
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="worms")
     * @ORM\JoinColumn(name="author_user_id", referencedColumnName="id", onDelete="SET NULL")
     * @var User
     */
    protected $author;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    protected $mode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *      min=10,
     *      max=3200,
     *      minMessage="La largeur doit être supérieure à 10px",
     *      maxMessage="La largeur doit être inférieure à 3200px"
     * )
     */
    protected $width;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *      min=10,
     *      max=3200,
     *      minMessage="La hauteur doit être supérieure à 10px",
     *      maxMessage="La hauteur doit être inférieure à 3200px"
     * )
     */
    protected $height;

    /**
     * @ORM\Column(type="integer", name="time_limit")
     * @Assert\NotBlank
     * @Assert\Range(
     *      min=30,
     *      max=43200,
     *      minMessage="Le temps limite doit être supérieur à 30 min (0.02 jours)",
     *      maxMessage="La temps limite doit être inférieur à 30 jours"
     * )
     * @var int
     */
    protected $timeLimit;

    /**
     * @ORM\Column(type="boolean", name="unique_queue")
     * @var bool
     */
    protected $uniqueQueue;

    /**
     * @ORM\OneToMany(targetEntity="Submission", mappedBy="worm")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $submissions;

    /**
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="worm", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position": "ASC"})
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $subscriptions;

    /**
     *
     */
    public function __construct()
    {
        $this->submissions = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->mode = static::MODE_HORIZONTAL;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Worm
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Worm
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set mode
     *
     * @param integer $mode
     * @return Worm
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return integer
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return mixed
     */
    public function getModeAsString()
    {
        $modes = static::getModes();

        return $modes[$this->getMode()];
    }

    /**
     * Set timeLimit
     *
     * @param integer $timeLimit
     * @return Worm
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    /**
     * Get timeLimit
     *
     * @return integer
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * Set uniqueQueue
     *
     * @param boolean $uniqueQueue
     * @return Worm
     */
    public function setUniqueQueue($uniqueQueue)
    {
        $this->uniqueQueue = $uniqueQueue;

        return $this;
    }

    /**
     * Get uniqueQueue
     *
     * @return boolean
     */
    public function getUniqueQueue()
    {
        return $this->uniqueQueue;
    }

    /**
     * Set author
     *
     * @param \Worm\SiteBundle\Entity\User $author
     * @return Worm
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Worm\SiteBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Submission $submission
     * @return $this
     */
    public function addSubmission(Submission $submission)
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions->add($submission);
            $submission->setWorm($this);
        }


        return $this;
    }

    /**
     * @param Submission $submission
     */
    public function removeSubmission(Submission $submission)
    {
        if ($this->submissions->contains($submission)) {
            $this->submissions->removeElement($submission);
            $submission->setWorm(null);
        }
    }

    /**
     * Get submissions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubmissions()
    {
        return $this->submissions;
    }

    /**
     * @param Subscription $subscription
     * @return $this
     */
    public function addSubscription(Subscription $subscription)
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setWorm($this);
        }


        return $this;
    }

    /**
     * @param Subscription $subscription
     */
    public function removeSubscription(Subscription $subscription)
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
            $subscription->setWorm(null);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @return Queue
     */
    public function getQueue()
    {
        return new Queue($this);
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

}