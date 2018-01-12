<?php

namespace Terox\SubscriptionBundle\Tests\Mock;

use Symfony\Component\Security\Core\User\UserInterface;

class UserMock implements UserInterface
{
    public function getRoles()
    {
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
    }

    public function eraseCredentials()
    {
    }
}