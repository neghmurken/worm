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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", name="queued_at")
     * @var \DateTime
     */
    protected $queuedAt;

    /**
     *
     */
    public function __construct()
    {
        $this->queuedAt = new \DateTime();
    }

    /**
     * @param \Worm\SiteBundle\Entity\Worm $worm
     */
    public function setWorm(Worm $worm)
    {
        $this->worm = $worm;
    }

    /**
     * @return \Worm\SiteBundle\Entity\Worm
     */
    public function getWorm()
    {
        return $this->worm;
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


}