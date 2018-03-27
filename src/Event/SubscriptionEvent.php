<?php

namespace Terox\SubscriptionBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;

class SubscriptionEvent extends Event
{
    /**
     * @var SubscriptionInterface
     */
    private $subscription;

    /**
     * @var bool
     */
    private $fromRenew;

    /**
     * Constructor.
     *
     * @param SubscriptionInterface $subscription
     * @param boolean               $fromRenew
     */
    public function __construct(SubscriptionInterface $subscription, $fromRenew = false)
    {
        $this->subscription = $subscription;
        $this->fromRenew    = $fromRenew;
    }

    /**
     * @return SubscriptionInterface
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return bool
     */
    public function isFromRenew()
    {
        return $this->fromRenew;
    }
}
