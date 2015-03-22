<?php

namespace Worm\SiteBundle\Queue;

use Symfony\Component\Config\Definition\Exception\Exception;
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
    }

    /**
     * @param $position
     * @throws \Exception
     */
    public function unsubscribe($position)
    {
        $atPos = $this->worm->getSubscriptions()->filter(
            function ($subscription) use ($position) {
                return $subscription->getPosition() === $position;
            }
        );

        if ($atPos->isEmpty()) {
            throw new \Exception('No subscription at position ' . $position);
        }

        $this->worm->removeSubscription($atPos->first());
    }

    /**
     * @return Subscription
     */
    public function getCurrent()
    {
        return $this->worm->getSubscriptions()->first();
    }

    /**
     *
     */
    public function next()
    {
        $current = $this->getCurrent();
        $this->worm->removeSubscription($current);

        $submission = new Submission();
        $submission->setAuthor($current->getUser());
        $submission->setWorm($this->worm);

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

}