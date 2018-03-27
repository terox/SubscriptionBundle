<?php

namespace Terox\SubscriptionBundle\Subscription;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Terox\SubscriptionBundle\Event\SubscriptionEvent;
use Terox\SubscriptionBundle\Event\SubscriptionEvents;
use Terox\SubscriptionBundle\Exception\PermanentSubscriptionException;
use Terox\SubscriptionBundle\Exception\ProductDefaultNotFoundException;
use Terox\SubscriptionBundle\Exception\StrategyNotFoundException;
use Terox\SubscriptionBundle\Exception\SubscriptionRenewalException;
use Terox\SubscriptionBundle\Exception\SubscriptionStatusException;
use Terox\SubscriptionBundle\Exception\SubscriptionIntegrityException;
use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;
use Terox\SubscriptionBundle\Registry\SubscriptionRegistry;
use Terox\SubscriptionBundle\Repository\SubscriptionRepositoryInterface;
use Terox\SubscriptionBundle\Strategy\SubscriptionStrategyInterface;

/**
 * Manages subscription workflow.
 *
 */
class SubscriptionManager
{
    /**
     * @var SubscriptionRegistry
     */
    private $registry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $config;

    /**
     * Constructor.
     *
     * @param SubscriptionRegistry            $registry               Registry of strategies
     * @param SubscriptionRepositoryInterface $subscriptionRepository Subscription repository
     * @param EventDispatcherInterface        $eventDispatcher        Event Dispatcher
     * @param array                           $config                 Bundle configuration
     */
    public function __construct(
        SubscriptionRegistry $registry,
        SubscriptionRepositoryInterface $subscriptionRepository,
        EventDispatcherInterface $eventDispatcher,
        $config
    )
    {
        $this->registry               = $registry;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->eventDispatcher        = $eventDispatcher;
        $this->config                 = $config;
    }

    /**
     * Create a new subscription with a determinate strategy.
     *
     * @param ProductInterface $product      Product that you want associate with subscription
     * @param UserInterface    $user         User to associate to subscription
     * @param string           $strategyName If you keep this null it will use product default strategy
     *
     * @return SubscriptionInterface
     *
     * @throws StrategyNotFoundException
     * @throws SubscriptionIntegrityException
     * @throws PermanentSubscriptionException
     */
    public function create(ProductInterface $product, UserInterface $user, $strategyName = null)
    {
        // Get strategy
        $strategyName = $strategyName ?? $product->getStrategyCodeName();
        $strategy     = $this->registry->get($strategyName);

        // Get current enabled subscriptions of product
        $subscriptions = $this->subscriptionRepository->findByProduct($product, $user);

        // Check that subscriptions collection are a valid objects
        foreach ($subscriptions as $activeSubscription) {
            $this->checkSubscriptionIntegrity($activeSubscription);
        }

        $subscription = $strategy->createSubscription($product, $subscriptions);
        $subscription->setStrategy($strategyName);
        $subscription->setUser($user);

        return $subscription;
    }

    /**
     * Activate subscription.
     *
     * @param SubscriptionInterface $subscription
     * @param boolean               $isRenew
     *
     * @throws SubscriptionIntegrityException
     * @throws StrategyNotFoundException
     * @throws SubscriptionStatusException
     * @throws ProductDefaultNotFoundException
     */
    public function activate(SubscriptionInterface $subscription, $isRenew = false)
    {
        $this->checkSubscriptionIntegrity($subscription);
        $this->checkSubscriptionNonActive($subscription);

        $strategy     = $this->getStrategyFromSubscription($subscription);
        $finalProduct = $strategy->getProductStrategy()->getFinalProduct($subscription->getProduct());

        $subscription->setProduct($finalProduct);
        $subscription->activate();

        $subscriptionEvent = new SubscriptionEvent($subscription, $isRenew);
        $this->eventDispatcher->dispatch(SubscriptionEvents::ACTIVATE_SUBSCRIPTION, $subscriptionEvent);
    }

    /**
     * Renew subscription that has been expired.
     *
     * @param SubscriptionInterface $subscription
     *
     * @return SubscriptionInterface New
     *
     * @throws SubscriptionIntegrityException
     * @throws SubscriptionRenewalException
     * @throws ProductDefaultNotFoundException
     * @throws StrategyNotFoundException
     * @throws PermanentSubscriptionException
     * @throws SubscriptionStatusException
     */
    public function renew(SubscriptionInterface $subscription)
    {
        $this->checkSubscriptionIntegrity($subscription);
        $this->checkSubscriptionRenewable($subscription);
        $this->checkSubscriptionActive($subscription);

        // Expire the last subscription
        $this->expire($subscription, 'renew', true);

        // Get the next renewal product
        $renewalProduct = $this->getRenewalProduct($subscription->getProduct());
        $strategy       = $this->getStrategyFromSubscription($subscription);
        $finalProduct   = $strategy->getProductStrategy()->getFinalProduct($renewalProduct);

        // Create new subscription (following the way of expired subscription)
        $newSubscription = $this->create($finalProduct, $subscription->getUser(), $finalProduct->getStrategyCodeName());
        $newSubscription->setAutoRenewal(true);

        // Activate the next subscription
        $this->activate($newSubscription, true);

        $subscriptionEvent = new SubscriptionEvent($newSubscription);
        $this->eventDispatcher->dispatch(SubscriptionEvents::RENEW_SUBSCRIPTION, $subscriptionEvent);

        return $newSubscription;
    }

    /**
     * Get next roll product.
     *
     * @param ProductInterface $product
     *
     * @return ProductInterface
     */
    protected function getRenewalProduct(ProductInterface $product)
    {
        if (null === $product->getNextRenewalProduct()) {
            return $product;
        }

        return $product->getNextRenewalProduct();
    }

    /**
     * Expire subscription.
     *
     * @param SubscriptionInterface $subscription
     * @param string                $reason The reason codename that you want set into the subscription
     * @param boolean               $isRenew
     */
    public function expire(SubscriptionInterface $subscription, $reason = 'expire', $isRenew = false)
    {
        $subscription->setReason($this->config['reasons'][$reason]);
        $subscription->deactivate();

        $subscriptionEvent = new SubscriptionEvent($subscription, $isRenew);
        $this->eventDispatcher->dispatch(SubscriptionEvents::EXPIRE_SUBSCRIPTION, $subscriptionEvent);
    }

    /**
     * Disable subscription.
     *
     * @param SubscriptionInterface $subscription
     */
    public function disable(SubscriptionInterface $subscription)
    {
        $subscription->setReason($this->config['reasons']['disable']);
        $subscription->deactivate();

        $subscriptionEvent = new SubscriptionEvent($subscription);
        $this->eventDispatcher->dispatch(SubscriptionEvents::DISABLE_SUBSCRIPTION, $subscriptionEvent);
    }

    /**
     * Get strategy from subscription.
     *
     * @param SubscriptionInterface $subscription
     *
     * @return SubscriptionStrategyInterface
     *
     * @throws StrategyNotFoundException
     */
    private function getStrategyFromSubscription(SubscriptionInterface $subscription)
    {
        $strategyName = $subscription->getStrategy();

        return $this->registry->get($strategyName);
    }

    /**
     * Check subscription integrity.
     *
     * @param SubscriptionInterface $subscription
     *
     * @throws SubscriptionIntegrityException
     */
    private function checkSubscriptionIntegrity(SubscriptionInterface $subscription)
    {
        if (null === $subscription->getProduct()) {
            throw new SubscriptionIntegrityException('Subscription must have a product defined.');
        }

        if (null === $subscription->getUser()) {
            throw new SubscriptionIntegrityException('Subscription must have a user defined.');
        }
    }

    /**
     * Check if subscription is auto-renewable.
     *
     * @param SubscriptionInterface $subscription
     *
     * @throws SubscriptionRenewalException
     */
    private function checkSubscriptionRenewable(SubscriptionInterface $subscription)
    {
        if (null === $subscription->getEndDate()) {
            throw new SubscriptionRenewalException('A permanent subscription can not be renewed.');
        }

        if (!$subscription->isAutoRenewal()) {
            throw new SubscriptionRenewalException('The current subscription is not auto-renewal.');
        }

        if (!$subscription->getProduct()->isAutoRenewal()) {
            throw new SubscriptionRenewalException(sprintf(
                'The product "%s" is not auto-renewal. Maybe is disabled?',
                $subscription->getProduct()->getName()
            ));
        }
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @throws SubscriptionStatusException
     */
    private function checkSubscriptionNonActive(SubscriptionInterface $subscription)
    {
        if (!$subscription->isActive()) {
            return;
        }

        throw new SubscriptionStatusException('Subscription is active.');
    }

    /**
     * @param SubscriptionInterface $subscription
     *
     * @throws SubscriptionStatusException
     */
    private function checkSubscriptionActive(SubscriptionInterface $subscription)
    {
        if ($subscription->isActive()) {
            return;
        }

        throw new SubscriptionStatusException('Subscription is not active.');
    }
}
