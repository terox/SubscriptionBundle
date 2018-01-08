SubscriptionBundle
==================

<img src="https://raw.githubusercontent.com/terox/SubscriptionBundle/master/doc/images/SubscriptionBundleLogo.png" alt="SubscriptionBundleLogo" align="right">

> SubscriptionBundle helps you to create and manage subscriptions services (also known as plans) for your users in your application.

The SubscriptionBundle fits perfectly in your Symfony application and your models. It don't cares about what persistence
layer are you using (a [http://www.doctrine-orm.org](Doctrine), [http://www.redis.io](Redis)...); it only provides an easy 
and solid base where start to handle this type of products in your Symfony application.

**Features**
 * Trying to maintain a easy, solid, well-documented and **agnostic** base to start to work without headaches.
 * Many actions allowed on to subscriptions: *active*, *expire*, *disable* and *renew* with his appropriate events.
 * **Extensible**: you can extend and change the out-of-the-box features creating your own strategies that determine how 
 a subscription should be handled to fit to your requirements.

**Requeriments**
 * Symfony 3.x applications
 
Documentation
-------------
* [Installation](#installation)
* The lifecycle of a subscription
* Strategies
** Product strategies
*** How to create a product strategy
*** Out-of-the-box strategies
**** Default product strategy
** Subscription strategies
*** How to create a subscription strategy
*** Out-of-the-box strategies:
**** End Last Strategy

* CookBooks
** Using into Doctrine based applications

Quick start
-----------

### 1. Download the bundle:

```bash
$ composer require terox/subscription-bundle
```

### 2. Enable the bundle in Symfony Application:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Terox\SubscriptionBundle\TeroxSubscriptionBundle(),
        );
    }

    // ...
}
```

### 3. Configure the bundle:

```yaml
terox_subscription:
    # Where is the subscription model located in your application
    # Remember that your model must implement the interface
    subscription_class: AppBundle\Entity\Subscription # Interface: Terox\SubscriptionBundle\Model\SubscriptionInterface

    # Repository services name
    # Remember that repositories must be implement the interfaces
    subscription_repository: app.repository.subscription # Interface: Terox\SubscriptionBundle\Repository\SubscriptionRepositoryInterface
    product_repository: app.repository.product           # Interface: Terox\SubscriptionBundle\Repository\ProductRepositoryInterface

    # Configure out-of-the-box strategies
    default_subscription_strategy: end_last
    default_product_strategy: product.default
```
Read the [complete configuration reference](https://github.com/terox/SubscriptionBundle/master/doc/ReferenceConfig.md)


# TODO
- product strategy
- only one subscription per product

License
-------

This software is published under the [MIT License](https://github.com/terox/SubscriptionBundle/master/LICENSE.md)