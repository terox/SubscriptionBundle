<?php

namespace Terox\SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SubscriptionStrategyCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $factory            = $container->findDefinition('terox.subscription.registry');
        $strategyServiceIds = array_keys($container->findTaggedServiceIds('subscription.strategy'));

        // Add subscription strategies to factory instance
        foreach($strategyServiceIds as $strategyServiceId) {
            $strategy = $container->findDefinition($strategyServiceId);
            $tag      = $strategy->getTag('subscription.strategy');

            if('subscription' !== $tag[0]['type']) {
                continue;
            }

            $factory->addMethodCall('addStrategy', [ new Reference($strategyServiceId), $tag[0]['strategy'] ]);
        }
    }
}
