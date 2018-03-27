<?php

namespace Terox\SubscriptionBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Terox\SubscriptionBundle\Command\AbstractCommand;
use Terox\SubscriptionBundle\Command\ActiveCommand;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class ActiveCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $application = new \Symfony\Component\Console\Application();
        $application->add(new ActiveCommand());

        /** @var AbstractCommand $command */
        $command = $application->find(TeroxSubscriptionBundle::COMMAND_NAMESPACE.':active');
        $command->setContainer($this->getMockContainer());

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'id'       => 1
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Activated subscription', $output);
    }
}