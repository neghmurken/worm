<?php

namespace Worm\SiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as FOSUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Worm\SiteBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="ws_user")
 */
class User extends FOSUser
{

    /**
     * @ORM\Id;
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Submission", mappedBy="author")
     */
    protected $submissions;

    /**
     * @ORM\OneToMany(targetEntity="Worm", mappedBy="author")
     * @var
     */
    protected $worms;

    /**
     * @ORM\OneToMany(targetEntity="Subscription", mappedBy="user")
     * @var
     */
    protected $subscriptions;

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
     *
     */
    public function __construct()
    {
        $this->worms = new ArrayCollection();
        $this->submissions = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();

        parent::__construct();
    }

    /**
     * @param Submission $submission
     * @return $this
     */
    public function addSubmission(Submission $submission)
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions->add($submission);
            $submission->setAuthor($this);
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
            $submission->setAuthor(null);
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
     * @param Worm $worm
     * @return $this
     */
    public function addWorm(Worm $worm)
    {
        if (!$this->worms->contains($worm)) {
            $this->worms->add($worm);
            $worm->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Worm $worm
     */
    public function removeWorm(Worm $worm)
    {
        if ($this->worms->contains($worm)) {
            $this->worms->removeElement($worm);
            $worm->setAuthor(null);
        }
    }

    /**
     * Get worms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorms()
    {
        return $this->worms;
    }

    /**
     * @param Subscription $subscription
     * @return $this
     */
    public function addSubscription(Subscription $subscription)
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setUser($this);
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
            $subscription->setUser(null);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }
}