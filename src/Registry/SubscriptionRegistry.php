<?php

namespace Terox\SubscriptionBundle\Registry;

use Psr\Log\InvalidArgumentException;
use Terox\SubscriptionBundle\Exception\StrategyNotFoundException;
use Terox\SubscriptionBundle\Strategy\SubscriptionStrategyInterface;

/**
 * Subscription strategy registry.
 *
 */
class SubscriptionRegistry
{
    /**
     * @var SubscriptionStrategyInterface[]
     */
    private $strategies;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $this->strategies = [];
    }

    /**
     * Add strategy.
     *
     * @param SubscriptionStrategyInterface $strategy
     * @param string                        $name
     *
     * @return SubscriptionRegistry
     *
     * @throws \InvalidArgumentException
     */
    public function addStrategy(SubscriptionStrategyInterface $strategy, $name)
    {
        if (array_key_exists($name, $this->strategies)) {
            throw new \InvalidArgumentException(sprintf('The strategy %s is already a registered strategy.', $name));
        }

        $this->strategies[$name] = $strategy;

        return $this;
    }

    /**
     * Get strategy.
     *
     * @param string $name
     *
     * @return SubscriptionStrategyInterface
     *
     * @throws StrategyNotFoundException
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->strategies)) {
            throw new StrategyNotFoundException(sprintf('The strategy "%s" does not exist in the registry', $name));
        }

        return $this->strategies[$name];
    }
}
