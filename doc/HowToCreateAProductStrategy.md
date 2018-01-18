How to create a product strategy
================================

The structure of a product strategy is very simple as you are going to see.

The **product strategy** is ***very related*** with **subscription strategy**. The fact, it is injected into [subscription
strategy constructor](https://github.com/terox/SubscriptionBundle/blob/master/src/Strategy/AbstractProductStrategy.php#L38). 
That means that subscription can use the product strategy to perform some actions.

## Create your own product strategy:

### Create the strategy class:

Place your strategy file where you consider more suitable in your file structure:

```php
<?php

namespace App\Strategy\Product;

use Terox\SubscriptionBundle\Strategy\AbstractProductStrategy;
use Terox\SubscriptionBundle\Model\ProductInterface;

class MyProductStrategy extends AbstractProductStrategy
{
    /**
     * This function will determine if we should continue using the current product (passed as argument) or other.
     * 
     * @param ProductInterface $product
     * 
     * @return ProductInterface
     */
    public function getFinalProduct(ProductInterface $product)
    {
        // ...
        // Your business logic
        // ...
        
        return $product; 
    }
    
}
````

#### ❗ Remember:
* Use in your product model the next interface: ***Terox\SubscriptionBundle\Model\ProductInterface***.
* Use in your repositories the next interface: ***Terox\SubscriptionBundle\Repository\ProductRepositoryInterface***.

Both classes must be [https://github.com/terox/SubscriptionBundle/blob/master/doc/ReferenceConfig.md](configurated).

### Declare the strategy into container:

This register the product strategy into the container.

```yaml
    app.strategy.my_product:
        class: App\Strategy\Product\MyProductStrategy
        parent: terox.subscription.strategy.product.abstract
        tags:
            - { name: subscription.strategy, type: product, strategy: my_product }
```

### Last considerations

Please keep in mind that should declare a new subscription strategy if you want use your 