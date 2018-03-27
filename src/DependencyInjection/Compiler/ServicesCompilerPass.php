<?php

namespace Terox\SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class ServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $productRepository      = $container->getParameter('terox_subscription.config.product.repository');
        $subscriptionRepository = $container->getParameter('terox_subscription.config.subscription.repository');

        $container->setAlias(str_replace('_', '.', 'terox_subscription').'.repository.product', $productRepository);
        $container->setAlias(str_replace('_', '.', 'terox_subscription').'.repository.subscription', $subscriptionRepository);
    }
}
