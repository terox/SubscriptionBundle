<?php

namespace Terox\SubscriptionBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Terox\SubscriptionBundle\DependencyInjection\Compiler\ServicesCompilerPass;
use Terox\SubscriptionBundle\DependencyInjection\Compiler\SubscriptionStrategyCompilerPass;

class TeroxSubscriptionBundle extends Bundle
{
    /**
     *{@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SubscriptionStrategyCompilerPass());
        $container->addCompilerPass(new ServicesCompilerPass());
    }
}
