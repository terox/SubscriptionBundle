<?php

namespace Terox\SubscriptionBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Terox\SubscriptionBundle\Repository\SubscriptionRepositoryInterface;
use Terox\SubscriptionBundle\Subscription\SubscriptionManager;
use Terox\SubscriptionBundle\Tests\Mock\SubscriptionMock;

class CommandTestCase extends TestCase
{
    protected function getMockContainer()
    {
        // Manager
        $manager = \Mockery::mock(SubscriptionManager::class);

        $manager
            ->shouldReceive('activate')
            ->once();

        $manager
            ->shouldReceive('disable')
            ->once();

        $manager
            ->shouldReceive('expire')
            ->once();

        // Repository
        $repository = \Mockery::mock(SubscriptionRepositoryInterface::class)
            ->shouldReceive('findById')
            ->withAnyArgs()
            ->andReturn(new SubscriptionMock())
            ->getMock();

        // Container
        $container = \Mockery::mock(Container::class);
        $container
            ->shouldReceive('get')
            ->once()
            ->with('terox.subscription.repository.subscription')
            ->andReturn($repository);

        $container
            ->shouldReceive('get')
            ->with('terox.subscription.manager')
            ->andReturn($manager);

        return $container;
    }

}