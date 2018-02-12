<?php

namespace Terox\SubscriptionBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
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
     * @param UserInterface    $user
     * @param boolean          $active
     *
     * @return SubscriptionInterface[]
     */
    public function findByProduct(ProductInterface $product, UserInterface $user, $active = true);
}
