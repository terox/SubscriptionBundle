<?php

namespace Terox\SubscriptionBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Terox\SubscriptionBundle\Command\AbstractCommand;
use Terox\SubscriptionBundle\Command\ExpireCommand;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class ExpireCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $application = new \Symfony\Component\Console\Application();
        $application->add(new ExpireCommand());

        /** @var AbstractCommand $command */
        $command = $application->find(TeroxSubscriptionBundle::COMMAND_NAMESPACE.':expire');
        $command->setContainer($this->getMockContainer());

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'id'       => 1,
            'reason'   => 'testing reason'
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Expired subscription', $output);
    }
}