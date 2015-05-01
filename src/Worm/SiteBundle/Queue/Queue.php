<?php

namespace Worm\SiteBundle\Queue;

use Worm\SiteBundle\Entity\Submission;
use Worm\SiteBundle\Entity\Subscription;
use Worm\SiteBundle\Entity\User;
use Worm\SiteBundle\Entity\Worm;

class Queue
{

    /**
     * @var Worm
     */
    protected $worm;

    private $_max;

    /**
     * @param Worm $worm
     */
    public function __construct(Worm $worm)
    {
        $this->worm = $worm;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function subscribe(User $user)
    {
        if (!$this->canRegister($user)) {
            throw new \Exception('User "' . $user->getUsername(
            ) . '" has already subscribed. This worm does not allow multiple subscriptions for one user');
        }

        $subscription = new Subscription();
        $subscription->setWorm($this->worm);
        $subscription->setUser($user);

        $nextPosition = $this->getMaxPosition() + 1;
        $subscription->setPosition($nextPosition);

        $this->_max = $nextPosition;

        $this->worm->addSubscription($subscription);

        $previous = $this->getPrevious($nextPosition);

        if (!$previous || $previous->isFinished()) {
            $subscription->setState(Subscription::STATE_CURRENT);
        }
    }

    /**
     * @param $position
     * @throws \Exception
     */
    public function withdraw($position)
    {
        $subscription = $this->findByPosition($position);

        if (false === $subscription) {
            throw new \Exception('No subscription at position ' . $position);
        }

        $wasCurrent = $subscription->getState() === $subscription::STATE_CURRENT;

        $subscription->setState(Subscription::STATE_WITHDRAWN);
        $subscription->setFinishedAt(new \DateTime());

        if ($wasCurrent) {
            $next = $this->getNext($position);
            if ($next) {
                $next->setState($subscription::STATE_CURRENT);
            }
        }
    }

    /**
     * @return Subscription
     */
    public function getCurrent()
    {
        return $this->worm->getSubscriptions()->filter(
            function ($subscription) {
                return $subscription->getState() === Subscription::STATE_CURRENT;
            }
        )->first();
    }

    /**
     *
     */
    public function next()
    {
        $current = $this->getCurrent();

        $submission = new Submission();
        $submission->setAuthor($current->getUser());
        $submission->setWorm($this->worm);
        $submission->setSubscription($current);

        $current->setState(Subscription::STATE_COMPLETE);
        $current->setFinishedAt(new \DateTime());

        $next = $this->getNext($current->getPosition());
        if ($next) {
            $next->setState(Subscription::STATE_CURRENT);
        }

        return $submission;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->worm->getSubscriptions()->isEmpty();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isRegistered(User $user)
    {
        return $this->worm->getSubscriptions()->exists(
            function ($key, $subscription) use ($user) {
                return $user->getId() == $subscription->getUser()->getId();
            }
        );
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canRegister(User $user)
    {
        return !($this->isRegistered($user) && $this->worm->getUniqueQueue());
    }

    /**
     * @return int
     */
    protected function getMaxPosition()
    {
        if (null === $this->_max) {
            $this->_max = 0;

            foreach ($this->worm->getSubscriptions() as $subscription) {
                $this->_max = max($this->_max, $subscription->getPosition());
            }
        }

        return $this->_max;
    }

    /**
     * @param $position
     * @return bool|Subscription
     */
    public function getNext($position)
    {
        return $this->findByPosition($position + 1);
    }

    /**
     * @param $position
     * @return bool|Subscription
     */
    public function getPrevious($position)
    {
        return $this->findByPosition($position - 1);
    }

    /**
     * @param $position
     * @return Subscription|bool
     */
    protected function findByPosition($position)
    {
        return $this->worm->getSubscriptions()->filter(
            function ($subscription) use ($position) {
                return $subscription->getPosition() === $position;
            }
        )->first();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActiveSubscriptions()
    {
        return $this->worm->getSubscriptions()->filter(
            function ($subscription) {
                return !$subscription->isFinished();
            }
        );
    }
}