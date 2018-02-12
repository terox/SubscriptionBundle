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
use Terox\SubscriptionBundle\Tests\Mock\UserMock;

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
     * @var UserMock
     */
    protected $user1;

    /**
     * @var UserMock
     */
    protected $user2;

    /**
     * @var UserMock
     */
    protected $user3;

    /**
     * @var UserMock
     */
    protected $user4;

    /**
     * @var \DateTimeImmutable
     */
    protected $subscription1EndDate;

    /**
     * @var \DateTimeImmutable
     */
    protected $subscription2EndDate;

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
     * @var  SubscriptionInterface
     */
    protected $currentSubscription4;

    /**
     * @var  SubscriptionInterface
     */
    protected $currentSubscription5;

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


        // Users
        $this->user1 = new UserMock();
        $this->user2 = new UserMock();
        $this->user3 = new UserMock();
        $this->user4 = new UserMock();


        // Product Base
        $this->product = \Mockery::mock(ProductInterface::class);
        $this->product->shouldReceive('getName')->andReturn('Product Base');
        $this->product->shouldReceive('getStrategyCodeName')->andReturn('end_last');


        // Product repository
        $this->productRepository = \Mockery::mock(ProductRepositoryInterface::class);


        // Subscriptions
        $this->subscription1EndDate = new \DateTimeImmutable();
        $this->currentSubscription1 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription1->shouldReceive('getEndDate')->andReturn($this->subscription1EndDate);
        $this->currentSubscription1->shouldReceive('getUser')->andReturn($this->user1);
        $this->currentSubscription1->shouldReceive('getProduct')->andReturn($this->product);
        //$this->currentSubscription1->shouldReceive('setStrategy');

        $this->subscription2EndDate = new \DateTimeImmutable('+10 days');
        $this->currentSubscription2 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription2->shouldReceive('getEndDate')->andReturn($this->subscription2EndDate->modify('+7 days'));
        $this->currentSubscription2->shouldReceive('getUser')->andReturn($this->user2);
        $this->currentSubscription2->shouldReceive('getProduct')->andReturn($this->product);
        //$this->currentSubscription2->shouldReceive('setStrategy');

        $this->currentSubscription3 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription3->shouldReceive('getEndDate')->andReturn($this->subscription2EndDate->modify('+3 days'));
        $this->currentSubscription3->shouldReceive('getUser')->andReturn($this->user2);
        $this->currentSubscription3->shouldReceive('getProduct')->andReturn($this->product);
        //$this->currentSubscription3->shouldReceive('setStrategy');

        $this->currentSubscription4 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription4->shouldReceive('getStartDate')->andReturn($this->subscription1EndDate);
        $this->currentSubscription4->shouldReceive('getEndDate')->andReturn($this->subscription1EndDate->modify('+15 days'));
        $this->currentSubscription4->shouldReceive('getUser')->andReturn($this->user1);
        $this->currentSubscription4->shouldReceive('getProduct')->andReturn($this->product);

        $this->currentSubscription5 = \Mockery::mock(SubscriptionInterface::class);
        $this->currentSubscription5->shouldReceive('getEndDate')->andReturn(new \DateTimeImmutable('+5 days'));
        $this->currentSubscription5->shouldReceive('getUser')->andReturn($this->user4);
        $this->currentSubscription5->shouldReceive('getProduct')->andReturn($this->product);

        $this->permanentSubscription = \Mockery::mock(SubscriptionInterface::class);
        $this->permanentSubscription->shouldReceive('getEndDate')->andReturn(null);
        $this->permanentSubscription->shouldReceive('getUser')->andReturn($this->user4);
        $this->permanentSubscription->shouldReceive('getProduct')->andReturn($this->product);
        $this->permanentSubscription->shouldReceive('setStrategy');
        $this->permanentSubscription->shouldReceive('setUser');


        // Subscription repository
        $this->subscriptionRepository = \Mockery::mock(SubscriptionRepositoryInterface::class);

        $this->subscriptionRepository
            ->shouldReceive('findByProduct')
            ->with($this->product, $this->user1)
            ->andReturn([
                $this->currentSubscription1,
                $this->currentSubscription4
            ]);

        $this->subscriptionRepository
            ->shouldReceive('findByProduct')
            ->with($this->product, $this->user2)
            ->andReturn([
                $this->currentSubscription2,
                $this->currentSubscription3
            ]);

        $this->subscriptionRepository
            ->shouldReceive('findByProduct')
            ->with($this->product, $this->user3)
            ->andReturn([
                $this->permanentSubscription
            ]);

        $this->subscriptionRepository
            ->shouldReceive('findByProduct')
            ->with($this->product, $this->user4)
            ->andReturn([
                $this->currentSubscription4,
                $this->permanentSubscription
            ]);

        // Default Product Strategy
        $this->defaultProductStrategy = new ProductDefaultStrategy(
            $this->productRepository,
            $this->subscriptionRepository,
            $this->logger
        );
    }
}
