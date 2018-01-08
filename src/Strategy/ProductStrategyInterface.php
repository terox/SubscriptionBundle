<?php

namespace Terox\SubscriptionBundle\Strategy;

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
     */
    public function getFinalProduct(ProductInterface $product);
}