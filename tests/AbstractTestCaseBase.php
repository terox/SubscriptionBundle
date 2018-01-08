<?php

namespace Terox\SubscriptionBundle\Tests;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Terox\SubscriptionBundle\Exception\PermanentSubscriptionException;
use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;
use Terox\SubscriptionBundle\Repository\ProductRepositoryInterface;
use Terox\SubscriptionBundle\Repository\SubscriptionRepositoryInterface;
use Terox\SubscriptionBundle\Strategy\ProductDefaultStrategy;
use Terox\SubscriptionBundle\Strategy\ProductStrategyInterface;
use Terox\SubscriptionBundle\Strategy\SubscriptionEndLastStrategy;
use Terox\SubscriptionBundle\Registry\SubscriptionRegistry;
use Terox\SubscriptionBundle\Tests\Mock\SubscriptionMock;

abstract class AbstractTestCaseBase extends TestCase
{
    const MONTH_SECONDS = 2592000;

    /**
     * @var Logger
     */
    protected $logger;
    
    protected $productRepository;
    
    /**
     * @var SubscriptionRepositoryInterface
     */
    protected $subscriptionRepository;

    /**
     * @var \DateTimeImmutable
     */
    protected $subscription1EndDate;

    /**
     * @var SubscriptionInterface
     */
    protected $currentSubscription1;

    /**
     * @var SubscriptionInterface
     */
    protected $currentSubscription2;

    /**
     * @var SubscriptionInterface
     */
    protected $currentSubscription3;

    /**
     * @var SubscriptionInterface
     */
    protected $permanentSubscription;
    
    protected $defaultProductStrategy;
    
    protected $product;
    
    public function setUp()
    {
        // Logger
        $this->logger = \Mockery::mock(Logger::class);
        $this->logger->shouldReceive('error');
        
        // Product repository
        $this->productRepository = \Mockery::mock(ProductRepositoryInterface::class); 
        
        // Subscription repository
        $this->subscriptionRepository = \Mockery::mock(SubscriptionRepositoryInterface::class);

        // Subscriptions
        $this->subscription1EndDate = new \DateTimeImmutable('+10 days');
        $this->currentSubscription1 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription1->shouldReceive('getEndDate')->andReturn($this->subscription1EndDate);
        $this->currentSubscription1->shouldReceive('getUser')->andReturn(new \stdClass());
        $this->currentSubscription1->shouldReceive('setStrategy');

        $this->currentSubscription2 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription2->shouldReceive('getEndDate')->andReturn($this->subscription1EndDate->modify('+5 days'));
        $this->currentSubscription2->shouldReceive('getUser')->andReturn(new \stdClass());
        $this->currentSubscription2->shouldReceive('setStrategy');

        $this->currentSubscription3 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription3->shouldReceive('getEndDate')->andReturn($this->subscription1EndDate->modify('+10 days'));
        $this->currentSubscription3->shouldReceive('getUser')->andReturn(new \stdClass());
        $this->currentSubscription3->shouldReceive('setStrategy');

        $this->permanentSubscription = \Mockery::mock(SubscriptionInterface::class);
        $this->permanentSubscription->shouldReceive('getEndDate')->andReturn(null);
        $this->permanentSubscription->shouldReceive('getUser')->andReturn(new \stdClass());
        $this->permanentSubscription->shouldReceive('setStrategy');

        // Default Product Strategy
        $this->defaultProductStrategy = new ProductDefaultStrategy(
            $this->productRepository,
            $this->subscriptionRepository,
            $this->logger
        );

        // Product Base
        $this->product = \Mockery::mock(ProductInterface::class);
        $this->product->shouldReceive('getName')->andReturn('Product A');
    }
}