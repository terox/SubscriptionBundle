<?php

namespace Terox\SubscriptionBundle\Strategy;

use Terox\SubscriptionBundle\Exception\PermanentSubscriptionException;
use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;

interface SubscriptionStrategyInterface
{
    /**
     * @param ProductInterface        $product       Product that will be used to create the new subscription
     * @param SubscriptionInterface[] $subscriptions Enabled subscriptions
     *
     * @return SubscriptionInterface
     *
     * @throws PermanentSubscriptionException
     */
    public function createSubscription(ProductInterface $product, array $subscriptions = []);

    /**
     * @return ProductStrategyInterface
     */
    public function getProductStrategy();
}
