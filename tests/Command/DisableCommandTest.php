<?php

namespace Terox\SubscriptionBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Terox\SubscriptionBundle\Command\AbstractCommand;
use Terox\SubscriptionBundle\Command\DisableCommand;
use Terox\SubscriptionBundle\TeroxSubscriptionBundle;

class DisableCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $application = new \Symfony\Component\Console\Application();
        $application->add(new DisableCommand());

        /** @var AbstractCommand $command */
        $command = $application->find(TeroxSubscriptionBundle::COMMAND_NAMESPACE.':disable');
        $command->setContainer($this->getMockContainer());

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'id'       => 1
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Disabled subscription', $output);
    }
}