<?php

namespace Worm\SiteBundle\Queue;

use Worm\SiteBundle\Entity\Subscription;
use Worm\SiteBundle\Entity\Worm;

class DueDateResolver
{
    protected $worm;

    /**
     * @param Worm $worm
     */
    public function __construct(Worm $worm)
    {
        $this->worm = $worm;
    }

    /**
     * @param Subscription $subscription
     * @return \DateTime|mixed|null
     */
    public function resolve(Subscription $subscription)
    {
        $queue = $this->worm->getQueue();
        $anchorDate = null;

        switch ($subscription->getState()) {
            case Subscription::STATE_CURRENT:
                $previous = $queue->getPrevious($subscription->getPosition());
                $anchorDate = $subscription->getQueuedAt();

                if ($previous) {
                    $anchorDate = $previous->getFinishedAt();
                }
                break;

            case Subscription::STATE_QUEUED:
                $previous = $queue->getPrevious($subscription->getPosition());

                if ($previous) {
                    $anchorDate = clone $this->resolve($previous);
                }
                break;

            case Subscription::STATE_WITHDRAWN:
            case Subscription::STATE_COMPLETE:
                return $subscription->getFinishedAt();

            default:
                break;
        }

        if ($anchorDate) {
            $date = clone $anchorDate;
            $date->add(new \DateInterval('PT' . $this->worm->getTimeLimit() . 'M'));

            return $date;
        }

        return null;
    }

}