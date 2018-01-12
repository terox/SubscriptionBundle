<?php

namespace Terox\SubscriptionBundle\Strategy;

use Terox\SubscriptionBundle\Exception\PermanentSubscriptionException;
use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;

/**
 * End Last Subscription Strategy.
 *
 * Starts a new subscription at the end of the latest if there isn't any permanent subscription with the current
 * product.
 */
class SubscriptionEndLastStrategy extends AbstractSubscriptionStrategy
{
    /**
     * {@inheritdoc}
     */
    public function createSubscription(ProductInterface $product, array $subscriptions = [])
    {
        if(empty($subscriptions)) {
            return $this->create($this->createCurrentDate(), $product);
        }

        $startDate = null;
        foreach($subscriptions as $subscription) {

            // Subscription is permanent, don't continue
            if(null === $subscription->getEndDate()) {
                $startDate = null;
                break;
            }

            // Catch the subscription with higher end date
            if(null === $startDate || $startDate < $subscription->getEndDate()) {
                $startDate = $subscription->getEndDate();
            }
        }

        // It's a permanent subscription
        if(null === $startDate) {

            if(count($subscriptions) > 1) {
                throw new PermanentSubscriptionException('More than one subscription per product is not allowed when there is a permanent enabled.');
            }

            return $subscriptions[0];
        }

        // Check if subscription is expired
        if(time() > $startDate->getTimestamp()) {
            $startDate = $this->createCurrentDate();
        }

        // Date should use the \DateTimeImmutable (a little fix)
        if(!$startDate instanceof \DateTimeImmutable) {
            $startDate = (new \DateTimeImmutable())->setTimestamp($startDate->getTimestamp());
        }

        return $this->create($startDate, $product);
    }

    /**
     * Create subscription.
     *
     * @param \DateTimeImmutable $startDate
     * @param ProductInterface   $product
     *
     * @return SubscriptionInterface
     */
    private function create($startDate, $product)
    {
        $endDate = null !== $product->getDuration() ?
            $startDate->modify(sprintf('+%s seconds', $product->getDuration())) : null;

        // Create the new subscription
        $subscription = $this->createSubscriptionInstance();
        $subscription->setProduct($product);
        $subscription->setStartDate($startDate);
        $subscription->setEndDate($endDate);
        $subscription->setAutoRenewal($product->isAutoRenewal());

        return $subscription;
    }
}