<?php

namespace Terox\SubscriptionBundle\Command;

use Terox\SubscriptionBundle\Model\SubscriptionInterface;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class DisableCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(TeroxSubscriptionBundle::COMMAND_NAMESPACE.':disable')
            ->setDescription('Disable a subscription');
    }

    /**
     * {@inheritdoc}
     */
    protected function action(SubscriptionInterface $subscription)
    {
        $this->getManager()->disable($subscription);

        $this->output->writeln(sprintf('Disabled subscription'));
    }
}
