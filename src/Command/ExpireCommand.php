<?php

namespace Terox\SubscriptionBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class ExpireCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(TeroxSubscriptionBundle::COMMAND_NAMESPACE.':expire')
            ->setDescription('Expire a subscription')
            ->addArgument(
                'reason',
                InputArgument::OPTIONAL,
                'Reason of expiration',
                'expired'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function action(SubscriptionInterface $subscription)
    {
        $reason = $this->input->getArgument('reason');

        $this->getManager()->expire($subscription, $reason);

        $this->output->writeln(sprintf('Expired subscription'));
    }
}
