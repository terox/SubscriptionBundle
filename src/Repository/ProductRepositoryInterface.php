<?php

namespace Terox\SubscriptionBundle\Repository;

use Terox\SubscriptionBundle\Model\ProductInterface;

interface ProductRepositoryInterface
{
    /**
     * Find a default product.
     *
     * @return ProductInterface|null
     */
    public function findDefault();
}