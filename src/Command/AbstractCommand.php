<?php

namespace Terox\SubscriptionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;
use Terox\SubscriptionBundle\Subscription\SubscriptionManager;

abstract class AbstractCommand extends ContainerAwareCommand
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'Subscription ID'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Save the input interfaces
        $this->input  = $input;
        $this->output = $output;

        $subscriptionId = $input->getArgument('id');
        $subscription   = $this->getContainer()->get('terox.subscription.repository.subscription')->findById($subscriptionId);

        if(null === $subscription) {
            return $output->writeln(sprintf('<error>The subscription with ID "%s" was not found.</error>', $subscriptionId));
        }

        // Execute the action
        $this->action($subscription);

        $output->writeln('<green>Finished.</green>');
    }

    /**
     * Action to execute when
     *
     * @param SubscriptionInterface $subscription
     *
     * @return void
     */
    abstract protected function action(SubscriptionInterface $subscription);

    /**
     * Get SubscriptionManager.
     *
     * @return SubscriptionManager
     */
    protected function getManager()
    {
        return $this->getContainer()->get('terox.subscription.manager');
    }
}
