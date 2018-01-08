<?php

namespace Terox\SubscriptionBundle\Strategy;

use Terox\SubscriptionBundle\Exception\ProductDefaultNotFoundException;
use Terox\SubscriptionBundle\Exception\ProductExpiredException;
use Terox\SubscriptionBundle\Exception\ProductIntegrityException;
use Terox\SubscriptionBundle\Exception\ProductQuoteExceededException;
use Terox\SubscriptionBundle\Model\ProductInterface;

class ProductDefaultStrategy extends AbstractProductStrategy
{
    /**
     * {@inheritdoc}
     */
    public function getFinalProduct(ProductInterface $product)
    {
        try {

            $this->checkProductIntegrity($product);
            $this->checkExpiration($product);
            $this->checkQuote($product);

            return $product;
        }

        catch(ProductIntegrityException $exception) {

            $this->getLogger()->error('Product integrity: {message}', [
                'message' => $exception->getMessage()
            ]);

        }

        catch(ProductExpiredException $exception) {

            $this->getLogger()->error('Product is expired: {message}', [
                'message' => $exception->getMessage()
            ]);

        }

        catch(ProductQuoteExceededException $exception) {

            $this->getLogger()->error('Product quota is exceeded: {message}', [
                'message' => $exception->getMessage()
            ]);

        }

        return $this->getDefaultProduct();
    }

    /**
     * Get default product in case of that current product is not valid.
     *
     * @return ProductInterface
     *
     * @throws ProductDefaultNotFoundException
     */
    private function getDefaultProduct()
    {
        $defaultProduct = $this->getProductRepository()->findDefault();

        if(null !== $defaultProduct) {
            return $defaultProduct;
        }

        throw new ProductDefaultNotFoundException('Default product was not found into the product repository');
    }
}