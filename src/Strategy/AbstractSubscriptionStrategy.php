<?php

namespace Terox\SubscriptionBundle\Strategy;

use Terox\SubscriptionBundle\Model\SubscriptionInterface;

abstract class AbstractSubscriptionStrategy implements SubscriptionStrategyInterface
{
    /**
     * @var string
     */
    private $subscriptionClass;

    /**
     * @var AbstractProductStrategy
     */
    private $productStrategy;

    /**
     * Constructor.
     *
     * @param string                  $subscriptionClass
     * @param AbstractProductStrategy $productStrategy
     */
    public function __construct($subscriptionClass, AbstractProductStrategy $productStrategy)
    {
        $this->subscriptionClass = $subscriptionClass;
        $this->productStrategy   = $productStrategy;
    }

    /**
     * @return SubscriptionInterface
     */
    public function createSubscriptionInstance()
    {
        return new $this->subscriptionClass();
    }

    /**
     * @return AbstractProductStrategy
     */
    public function getProductStrategy()
    {
        return $this->productStrategy;
    }

    /**
     * Create current date.
     *
     * @return \DateTimeImmutable
     */
    protected function createCurrentDate()
    {
        return new \DateTimeImmutable();
    }
}