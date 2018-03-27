<?php

namespace Terox\SubscriptionBundle\Command;

use Terox\SubscriptionBundle\Model\SubscriptionInterface;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class ActiveCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(TeroxSubscriptionBundle::COMMAND_NAMESPACE.':active')
            ->setDescription('Active a subscription a expired/disabled subscription');
    }

    /**
     * {@inheritdoc}
     */
    protected function action(SubscriptionInterface $subscription)
    {
        $this->getManager()->activate($subscription, false);

        $this->output->writeln(sprintf('Activated subscription'));
    }
}
