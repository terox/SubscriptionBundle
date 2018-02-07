<?php

namespace Terox\SubscriptionBundle\Strategy;

use Terox\SubscriptionBundle\Exception\ProductDefaultNotFoundException;
use Terox\SubscriptionBundle\Model\ProductInterface;

interface ProductStrategyInterface
{
    /**
     * Get final product.
     *
     * Determine the final based on your own algorithms.
     *
     * @param ProductInterface $product
     *
     * @return ProductInterface
     *
     * @throws ProductDefaultNotFoundException
     */
    public function getFinalProduct(ProductInterface $product);
}
