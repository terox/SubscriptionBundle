<?php

namespace Terox\SubscriptionBundle\Strategy;

use Monolog\Logger;
use Terox\SubscriptionBundle\Exception\ProductExpiredException;
use Terox\SubscriptionBundle\Exception\ProductIntegrityException;
use Terox\SubscriptionBundle\Exception\ProductQuoteExceededException;
use Terox\SubscriptionBundle\Model\ProductInterface;
use Terox\SubscriptionBundle\Repository\ProductRepositoryInterface;
use Terox\SubscriptionBundle\Repository\SubscriptionRepositoryInterface;

abstract class AbstractProductStrategy implements ProductStrategyInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SubscriptionRepositoryInterface
     */
    private $subscriptionRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param ProductRepositoryInterface      $productRepository
     * @param SubscriptionRepositoryInterface $subscriptionRepository
     * @param Logger                          $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        Logger $logger
    )
    {
        $this->productRepository      = $productRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger                 = $logger;
    }

    /**
     * @return ProductRepositoryInterface
     */
    protected function getProductRepository()
    {
        return $this->productRepository;
    }

    /**
     * @return SubscriptionRepositoryInterface
     */
    protected function getSubscriptionRepository()
    {
        return $this->subscriptionRepository;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Check the product model integrity.
     *
     * @param ProductInterface $product
     *
     * @throws ProductIntegrityException
     */
    final public function checkProductIntegrity(ProductInterface $product)
    {
        if($product->isDefault() && null !== $product->getQuota()) {

            throw new ProductIntegrityException(sprintf(
                'The product "%s" is a default product with a quota (%s). Default products can not have a quote value.',
                $product->getName(),
                $product->getQuota()
            ));

        }

        if($product->isDefault() && null !== $product->getExpirationDate()) {

            throw new ProductIntegrityException(sprintf(
                'The product "%s" is a default product with expiration date (%s). Default products can not have a expiration date.',
                $product->getName(),
                $product->getExpirationDate()->format('Y-m-d H:i:s')
            ));

        }
    }

    /**
     * Check product expiration.
     *
     * @param ProductInterface $product
     *
     * @throws ProductExpiredException
     */
    public function checkExpiration(ProductInterface $product)
    {
        $expirationDate = $product->getExpirationDate();

        if (null === $expirationDate || new \DateTime() <= $expirationDate) {
            return;
        }

        throw new ProductExpiredException(sprintf(
            'The product "%s" has been expired at %s.',
            $product->getName(),
            $expirationDate->format('Y-m-d H:i:s')
        ));
    }

    /**
     * Check product quote.
     *
     * @param ProductInterface $product
     *
     * @throws ProductQuoteExceededException
     */
    public function checkQuote(ProductInterface $product)
    {
        // Unlimited quote
        if(null === $product->getQuota()) {
            return;
        }

        // Calculate the current quote
        $currentQuote = $this->subscriptionRepository->getNumberOfSubscriptionsByProducts($product);

        if($currentQuote < $product->getQuota()) {
            return;
        }

        throw new ProductQuoteExceededException(sprintf(
            'The product "%s" quota is %s. This is exceeded. Increase the quota.',
            $product->getName(),
            $product->getQuota()
        ));
    }
}