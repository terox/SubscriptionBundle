<?php

namespace Terox\SubscriptionBundle\Tests\Mock;

use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Model\SubscriptionInterface;

class SubscriptionMock implements SubscriptionInterface
{
    private $user;

    private $startDate;

    private $endDate;

    private $product;

    private $active;

    private $autoRenewal;

    private $reason;

    private $strategy;

    public function __construct()
    {
        $this->setActive(false);
        $this->setAutoRenewal(false);
        $this->setUser(new \stdClass());
        $this->setStrategy('end_last'); // By default, only in this mock
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartDate(\DateTimeImmutable $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     *
     * @return SubscriptionMock
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAutoRenewal()
    {
        return $this->autoRenewal;
    }

    /**
     * @param mixed $autoRenewal
     *
     * @return SubscriptionMock
     */
    public function setAutoRenewal($autoRenewal)
    {
        $this->autoRenewal = $autoRenewal;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * {@inheritdoc}
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function activate()
    {
        $this->setActive(true);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate()
    {
        $this->setActive(false);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAutoRenewal()
    {
        return $this->autoRenewal;
    }

    /**
     * @param string $name
     *
     * @return SubscriptionInterface
     */
    public function setStrategy($name)
    {
        $this->strategy = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getStrategy()
    {
        return $this->strategy;
    }
}
