<?php

namespace Terox\SubscriptionBundle\Repository;

use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;

interface SubscriptionRepositoryInterface
{
    /**
     * Get number of subscriptions with associated product without regard to the state.
     *
     * @param ProductInterface $product
     *
     * @return integer
     */
    public function getNumberOfSubscriptionsByProducts(ProductInterface $product);

    /**
     * Find subscriptions by product and state.
     *
     * @param ProductInterface $product
     * @param boolean          $active
     *
     * @return SubscriptionInterface[]
     */
    public function findByProduct(ProductInterface $product, $active = true);
}
