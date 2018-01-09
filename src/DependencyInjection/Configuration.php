<?php

namespace Terox\SubscriptionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(TeroxSubscriptionBundle::BUNDLE_NAMESPACE);

        $rootNode
            ->children()
                ->scalarNode('subscription_class')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()

                ->scalarNode('subscription_repository')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('product_repository')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()

                ->scalarNode('default_subscription_strategy')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->defaultValue('end_last')
                ->end()

                ->arrayNode('reasons')
                    ->children()
                        ->scalarNode('renew')
                            ->defaultValue('Subscription expired and auto-renewal')
                        ->end()
                        ->scalarNode('expire')
                            ->defaultValue('Subscription expired')
                        ->end()
                        ->scalarNode('disable')
                            ->defaultValue('Subscription disabled')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
