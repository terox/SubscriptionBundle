<?php

namespace Terox\SubscriptionBundle\Model;

/**
 * SubscriptionInterface
 *
 */
interface SubscriptionInterface
{
    /**
     * @return object
     */
    public function getUser();

    /**
     * @param object $user
     *
     * @return SubscriptionInterface
     */
    public function setUser($user);

    /**
     * @return \DateTimeImmutable
     */
    public function getStartDate();

    /**
     * @param \DateTimeImmutable $dateTime
     *
     * @return SubscriptionInterface
     */
    public function setStartDate(\DateTimeImmutable $dateTime);

    /**
     * @return \DateTimeImmutable|null
     */
    public function getEndDate();

    /**
     * @param null|\DateTimeImmutable $dateTime
     *
     * @return SubscriptionInterface
     */
    public function setEndDate($dateTime);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     *
     * @return mixed
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return boolean
     */
    public function isActive();

    /**
     * @return SubscriptionInterface
     */
    public function activate();

    /**
     * @return SubscriptionInterface
     */
    public function deactivate();

    /**
     * @param boolean $renewal
     *
     * @return SubscriptionInterface
     */
    public function setAutoRenewal($renewal);

    /**
     * @return boolean
     */
    public function isAutoRenewal();

    /**
     * @param string $reason
     *
     * @return mixed
     */
    public function setReason($reason);

    /**
     * @return string
     */
    public function getReason();

    /**
     * @param string $name
     *
     * @return SubscriptionInterface
     */
    public function setStrategy($name);

    /**
     * @return string
     */
    public function getStrategy();
}