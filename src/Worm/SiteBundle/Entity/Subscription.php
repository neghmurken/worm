<?php

namespace Worm\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Worm\SiteBundle\Entity\Repository\SubscriptionRepository")
 * @ORM\Table(name="ws_subscription")
 * Class Subscription
 * @package Worm\SiteBundle\Entity
 */
class Subscription
{

    const STATE_QUEUED = 1;
    const STATE_CURRENT = 2;
    const STATE_COMPLETE = 3;
    const STATE_WITHDRAWN = 4;

    /**
     * @ORM\ManyToOne(targetEntity="Worm", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="worm_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\Id
     * @var Worm
     */
    protected $worm;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @var int
     */
    protected $position;

    /**
     * @ORM\Column(type="datetime", name="queued_at")
     * @var \DateTime
     */
    protected $queuedAt;

    /**
     * @ORM\Column(type="datetime", name="finished_at", nullable=true)
     */
    protected $finishedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="integer")
     */
    protected $state;

    /**
     * @ORM\OneToOne(targetEntity="Submission", inversedBy="subscription")
     * @ORM\JoinColumn(name="submission_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $submission;

    /**
     *
     */
    public function __construct()
    {
        $this->queuedAt = new \DateTime();
        $this->state = static::STATE_QUEUED;
    }

    /**
     * @param \Worm\SiteBundle\Entity\Worm $worm
     */
    public function setWorm(Worm $worm = null)
    {
        $this->worm = $worm;
    }

    /**
     * @return Worm
     */
    public function getWorm()
    {
        return $this->worm;
    }

    /**
     * @param \DateTime $queuedAt
     */
    public function setQueuedAt($queuedAt)
    {
        $this->queuedAt = $queuedAt;
    }

    /**
     * @return \DateTime
     */
    public function getQueuedAt()
    {
        return $this->queuedAt;
    }

    /**
     * @param \Worm\SiteBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Worm\SiteBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $finishedAt
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return mixed
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $submission
     */
    public function setSubmission(Submission $submission = null)
    {
        if (null !== $submission) {
            $this->submission = $submission;
            $submission->setSubscription($this);
        } else {
            if ($this->submission) {
                $this->submission->setSubscription(null);
            }
            $this->submission = null;
        }
    }

    /**
     * @return mixed
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return in_array($this->getState(), array(static::STATE_WITHDRAWN, static::STATE_COMPLETE));
    }

}