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
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255)
     * @Assert\NotBlank
     * @var string
     */
    protected $name;

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
     * @ORM\Column(type="integer", name="time_limit")
     * @Assert\NotBlank
     * @Assert\Range(min=30, max=43200)
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
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="worm")
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
}