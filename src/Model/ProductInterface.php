<?php

namespace Terox\SubscriptionBundle\Model;

/**
 * ProductInterface
 */
interface ProductInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return \DateInterval
     */
    public function getDuration();

    /**
     * @return \DateTime
     */
    public function getExpirationDate();

    /**
     * @return integer
     */
    public function getQuota();

    /**
     * @return boolean
     */
    public function isAutoRenewal();

    /**
     * @return boolean
     */
    public function isDefault();

    /**
     * @return string
     */
    public function getStrategyCodeName();

    /**
     * @return ProductInterface
     */
    public function getNextRenewalProduct();
}