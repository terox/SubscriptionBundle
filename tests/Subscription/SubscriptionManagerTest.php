<?php

namespace Terox\SubscriptionBundle\Tests\Subscription;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Terox\SubscriptionBundle\Exception\PermanentSubscriptionException;
use Terox\SubscriptionBundle\Exception\SubscriptionRenewalException;
use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;
use Terox\SubscriptionBundle\Strategy\SubscriptionEndLastStrategy;
use Terox\SubscriptionBundle\Registry\SubscriptionRegistry;
use Terox\SubscriptionBundle\Subscription\SubscriptionManager;
use Terox\SubscriptionBundle\Tests\AbstractTestCaseBase;
use Terox\SubscriptionBundle\Tests\Mock\SubscriptionMock;
use Terox\SubscriptionBundle\Tests\Mock\UserMock;

class SubscriptionManagerTest extends AbstractTestCaseBase
{
    /**
     * @var SubscriptionManager
     */
    private $subscriptionManager;

    public function setUp()
    {
        parent::setUp();

        // Product
        $this->product->shouldReceive('getName')->andReturn('Test non-default product');
        $this->product->shouldReceive('getDuration')->andReturn(self::MONTH_SECONDS);
        $this->product->shouldReceive('isDefault')->andReturn(false);
        $this->product->shouldReceive('getExpirationDate')->andReturn(null);
        $this->product->shouldReceive('getQuota')->andReturn(null);
        $this->product->shouldReceive('isAutoRenewal')->andReturn(false);

        // Repositories
        $this->productRepository->shouldReceive('findDefault')->andReturn($this->product);
        $this->subscriptionRepository->shouldReceive('getNumberOfSubscriptionsByProducts')->andReturn(50);

        // Registry
        $registry = new SubscriptionRegistry();
        $registry->addStrategy(
            new SubscriptionEndLastStrategy(SubscriptionMock::class, $this->defaultProductStrategy),
            'end_last'
        );

        $eventDispatcher = \Mockery::mock(EventDispatcher::class);
        $eventDispatcher->shouldReceive('dispatch');

        // Manager
        $this->subscriptionManager = new SubscriptionManager($registry, $this->subscriptionRepository, $eventDispatcher, [
            'default_subscription_strategy' => 'end_last',
            'reasons' => [
                'expire'  => 'EXPIRE_TEXT',
                'disable' => 'DISABLE_TEXT',
                'renew'   => 'RENEW_TEXT'
            ]
        ]);
    }

    public function testConcatNewSubscriptionWithPrevious()
    {
        $this->currentSubscription1->shouldReceive('getProduct')->andReturn($this->product);

        $this->subscriptionRepository->shouldReceive('findByProduct')->andReturn([
            $this->currentSubscription1
        ]);

        // Create new subscription
        $subscription = $this->subscriptionManager->create($this->product, 'end_last');

        $this->assertEquals(
            $this->subscription1EndDate->modify('+30 days')->getTimestamp(),
            $subscription->getEndDate()->getTimestamp()
        );

        $this->assertEquals(false, $subscription->isAutoRenewal());
    }

    public function testConcatNewSubscriptionWithOverlapedSubscriptions()
    {
        $this->currentSubscription2->shouldReceive('getProduct')->andReturn($this->product);
        $this->currentSubscription3->shouldReceive('getProduct')->andReturn($this->product);
        $this->currentSubscription3->shouldReceive('getUser')->andReturn(new UserMock());

        $this->subscriptionRepository->shouldReceive('findByProduct')->andReturn([
            $this->currentSubscription2,
            $this->currentSubscription3
        ]);

        // Create new subscription
        $subscription = $this->subscriptionManager->create($this->product, 'end_last');

        $this->assertEquals(
            $this->subscription1EndDate->modify('+40 days')->getTimestamp(),
            $subscription->getEndDate()->getTimestamp(),
            sprintf('Expected %s => %s',
                $this->subscription1EndDate->modify('+40 days')->format('Y-m-d H:i:s'),
                $subscription->getEndDate()->format('Y-m-d H:i:s')
            )
        );

        $this->assertEquals(false, $subscription->isAutoRenewal());
    }

    public function testSameSubscriptionOnPermanentSubscription()
    {
        $this->permanentSubscription->shouldReceive('getProduct')->andReturn($this->product);

        $this->subscriptionRepository->shouldReceive('findByProduct')->andReturn([
            $this->permanentSubscription
        ]);

        $subscription = $this->subscriptionManager->create($this->product, 'end_last');

        $this->assertInstanceOf(SubscriptionInterface::class, $subscription);
        $this->assertEquals(null, $subscription->getEndDate());
        $this->assertEquals($this->permanentSubscription, $subscription);
    }

    public function testSameSubscriptionOnPermanentWithFiniteSubscriptions()
    {
        $this->currentSubscription1->shouldReceive('getProduct')->andReturn($this->product);
        $this->permanentSubscription->shouldReceive('getProduct')->andReturn($this->product);

        $this->subscriptionRepository->shouldReceive('findByProduct')->andReturn([
            $this->permanentSubscription,
            $this->currentSubscription1,
        ]);

        $this->expectException(PermanentSubscriptionException::class);

        $this->subscriptionManager->create($this->product, 'end_last');
    }

    public function testActivateSubscriptionWithValidProduct()
    {
        $subscription = new SubscriptionMock();
        $subscription->setActive(false);
        $subscription->setProduct($this->product);

        $this->subscriptionManager->activate($subscription);

        $this->assertEquals(true, $subscription->getActive());
        $this->assertEquals($this->product, $subscription->getProduct());
    }

    public function testActivateSubscriptionWithExpiredProductAndDefault()
    {
        $productExpired = \Mockery::mock(ProductInterface::class);
        $productExpired->shouldReceive('isDefault')->andReturn(false);
        $productExpired->shouldReceive('getExpirationDate')->andReturn(new \DateTime('-1 hour'));
        $productExpired->shouldReceive('getName')->andReturn('Test non-default product with expiration date');
        $productExpired->shouldReceive('getQuota')->andReturn(null);

        $subscription = new SubscriptionMock();
        $subscription->setProduct($productExpired);

        $this->subscriptionManager->activate($subscription);

        $this->assertEquals(true, $subscription->getActive());
        $this->assertEquals($this->product, $subscription->getProduct());
    }

    public function testActivateSubscriptionWithQuoteExceededAndDefault()
    {
        $productQuota = \Mockery::mock(ProductInterface::class);
        $productQuota->shouldReceive('isDefault')->andReturn(false);
        $productQuota->shouldReceive('getExpirationDate')->andReturn(null);
        $productQuota->shouldReceive('getName')->andReturn('Test non-default product with quota exceeded');
        $productQuota->shouldReceive('getQuota')->andReturn(50);

        $subscription = new SubscriptionMock();
        $subscription->setProduct($productQuota);

        $this->subscriptionManager->activate($subscription);

        $this->assertEquals(true, $subscription->getActive());
        $this->assertEquals($this->product, $subscription->getProduct());
    }

    public function testActivateSubscriptionWithoutQuoteExceededAndDefault()
    {
        $productQuota = \Mockery::mock(ProductInterface::class);
        $productQuota->shouldReceive('isDefault')->andReturn(false);
        $productQuota->shouldReceive('getExpirationDate')->andReturn(null);
        $productQuota->shouldReceive('getName')->andReturn('Test non-default product without quota exceeded');
        $productQuota->shouldReceive('getQuota')->andReturn(51);
        $productQuota->shouldReceive('isAutoRenewal')->andReturn(false);

        $subscription = new SubscriptionMock();
        $subscription->setProduct($productQuota);

        $this->subscriptionManager->activate($subscription);

        $this->assertEquals(true, $subscription->getActive());
        $this->assertEquals(false, $subscription->isAutoRenewal());
        $this->assertEquals($productQuota, $subscription->getProduct());
    }

    public function testRenewPermanentSubscriptionFail()
    {
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($this->product);
        $subscription->setAutoRenewal(false);
        $subscription->setEndDate(null);

        $this->expectException(SubscriptionRenewalException::class);
        $this->expectExceptionMessage('A permanent subscription can not be renewed.');

        $this->subscriptionManager->renew($subscription);
    }

    public function testRenewSubscriptionNotEnabledAtSubscription()
    {
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($this->product);
        $subscription->setAutoRenewal(false);
        $subscription->setEndDate(new \DateTimeImmutable());

        $this->expectException(SubscriptionRenewalException::class);
        $this->expectExceptionMessage('The current subscription is not auto-renewal.');

        $this->subscriptionManager->renew($subscription);
    }

    public function testRenewSubscriptionNotEnabledASubscription()
    {
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($this->product);
        $subscription->setAutoRenewal(true);
        $subscription->setEndDate(new \DateTimeImmutable());

        $this->expectException(SubscriptionRenewalException::class);
        $this->expectExceptionMessage('The product "'.$this->product->getName().'" is not auto-renewal. Maybe is disabled?');

        $this->subscriptionManager->renew($subscription);
    }

    public function testRenewSubscriptionNotEnableAtProduct()
    {
        // Product
        $product = \Mockery::mock(ProductInterface::class);
        $product->shouldReceive('getName')->andReturn('Test non-default product without quota exceeded');
        $product->shouldReceive('getDuration')->andReturn(self::MONTH_SECONDS);
        $product->shouldReceive('isDefault')->andReturn(false);
        $product->shouldReceive('getExpirationDate')->andReturn(null);
        $product->shouldReceive('getQuota')->andReturn(null);
        $product->shouldReceive('isAutoRenewal')->andReturn(true);
        $product->shouldReceive('getNextRenewalProduct')->andReturn(null);
        $product->shouldReceive('getStrategyCodeName')->andReturn('end_last');

        // Subscription to renew
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($product);
        $subscription->setAutoRenewal(true);
        $subscription->setEndDate(new \DateTimeImmutable());

        // No current active subscriptions
        $this->subscriptionRepository->shouldReceive('findByProduct')->andReturn([]);

        // Renew subscription
        $newSubscription = $this->subscriptionManager->renew($subscription);

        $this->assertEquals(false, $subscription->isActive());
        $this->assertEquals('RENEW_TEXT', $subscription->getReason());

        $this->assertEquals(true, $newSubscription->isActive());
        $this->assertEquals(true, $newSubscription->isAutoRenewal());
        $this->assertEquals($subscription->getUser(), $newSubscription->getUser());
    }

    public function testRenewSubscriptionWithSameProduct()
    {
        // Product
        $product = \Mockery::mock(ProductInterface::class);
        $product->shouldReceive('getName')->andReturn('Test non-default product without quota exceeded');
        $product->shouldReceive('getDuration')->andReturn(self::MONTH_SECONDS);
        $product->shouldReceive('isDefault')->andReturn(false);
        $product->shouldReceive('getExpirationDate')->andReturn(null);
        $product->shouldReceive('getQuota')->andReturn(null);
        $product->shouldReceive('isAutoRenewal')->andReturn(true);
        $product->shouldReceive('getNextRenewalProduct')->andReturn(null);
        $product->shouldReceive('getStrategyCodeName')->andReturn('end_last');

        // Subscription to renew
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($product);
        $subscription->setAutoRenewal(true);
        $subscription->setEndDate(new \DateTimeImmutable());

        // No current active subscriptions
        $this->subscriptionRepository->shouldReceive('findByProduct')->andReturn([]);

        // Renew subscription
        $newSubscription = $this->subscriptionManager->renew($subscription);

        $this->assertEquals(false, $subscription->isActive());
        $this->assertEquals('RENEW_TEXT', $subscription->getReason());

        $this->assertEquals(true, $newSubscription->isActive());
        $this->assertEquals(true, $newSubscription->isAutoRenewal());
        $this->assertEquals($product, $newSubscription->getProduct());
        $this->assertEquals($subscription->getUser(), $newSubscription->getUser());
    }

    public function testRenewSubscriptionWithDifferentProduct()
    {
        // Product
        $product = \Mockery::mock(ProductInterface::class);
        $product->shouldReceive('getName')->andReturn('Test non-default product without quota exceeded');
        $product->shouldReceive('getDuration')->andReturn(self::MONTH_SECONDS);
        $product->shouldReceive('isDefault')->andReturn(false);
        $product->shouldReceive('getExpirationDate')->andReturn(null);
        $product->shouldReceive('getQuota')->andReturn(null);
        $product->shouldReceive('isAutoRenewal')->andReturn(true);
        $product->shouldReceive('getNextRenewalProduct')->andReturn($this->product); // This is the important step

        // Add to the default product strategy name
        $this->product->shouldReceive('getStrategyCodeName')->andReturn('end_last');

        // Subscription to renew
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($product);
        $subscription->setAutoRenewal(true);
        $subscription->setEndDate(new \DateTimeImmutable());

        // No current active subscriptions
        $this->subscriptionRepository->shouldReceive('findByProduct')->andReturn([]);

        // Renew subscription
        $newSubscription = $this->subscriptionManager->renew($subscription);

        $this->assertEquals(false, $subscription->isActive());
        $this->assertEquals('RENEW_TEXT', $subscription->getReason());

        $this->assertEquals(true, $newSubscription->isActive());
        $this->assertEquals(true, $newSubscription->isAutoRenewal());
        $this->assertEquals($subscription->getUser(), $newSubscription->getUser());
    }

    public function testExpireSubscriptionNotEnableAtProduct()
    {
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($this->product);

        $this->subscriptionManager->expire($subscription);

        $this->assertEquals(false, $subscription->getActive());
        $this->assertEquals('EXPIRE_TEXT', $subscription->getReason());
    }

    public function testDisableSubscription()
    {
        $subscription = new SubscriptionMock();
        $subscription->setActive(true);
        $subscription->setProduct($this->product);

        $this->subscriptionManager->disable($subscription);

        $this->assertEquals(false, $subscription->getActive());
        $this->assertEquals('DISABLE_TEXT', $subscription->getReason());
    }
}