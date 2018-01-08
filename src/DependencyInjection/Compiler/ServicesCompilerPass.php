<?php

namespace Terox\SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class ServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $namespace              = TeroxSubscriptionBundle::BUNDLE_NAMESPACE;
        $productRepository      = $container->getParameter($namespace.'.config.product.repository');
        $subscriptionRepository = $container->getParameter($namespace.'.config.subscription.repository');

        $container->setAlias(str_replace('_', '.', $namespace).'.repository.product', $productRepository);
        $container->setAlias(str_replace('_', '.', $namespace).'.repository.subscription', $subscriptionRepository);
    }
}
