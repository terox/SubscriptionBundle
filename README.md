SubscriptionBundle
[![Build Status](https://travis-ci.org/terox/SubscriptionBundle.svg?branch=master)](https://travis-ci.org/terox/SubscriptionBundle)
[![Code Coverage](https://scrutinizer-ci.com/g/terox/SubscriptionBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/terox/SubscriptionBundle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/terox/SubscriptionBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/terox/SubscriptionBundle/?branch=master)
==================

<img src="https://raw.githubusercontent.com/terox/SubscriptionBundle/master/doc/images/SubscriptionBundleLogo.png" alt="SubscriptionBundleLogo" width="242" height="212" align="right">

> SubscriptionBundle helps you to create and manage subscriptions services (also known as plans) for your users in your application.

The SubscriptionBundle fits perfectly in your Symfony application and your models. It don't cares about what persistence
layer are you using (a [http://www.doctrine-orm.org](Doctrine), [http://www.redis.io](Redis)...); it only provides an easy 
and solid base where start to handle this type of products in your Symfony applications.

**Features**
 * Trying to maintain a easy, solid, well-documented and **agnostic** base to start to work without headaches.
 * Many actions allowed on to subscriptions: *active*, *expire*, *disable* and *renew* with his appropriate events.
 * **Extensible**: you can extend and change the out-of-the-box features creating your own strategies that determine how 
 a subscription should be handled to fit to your requirements.

**Compatible**
 * Symfony 3.3+/4+ applications with Doctrine
 
Documentation
-------------

* [Quick Start](#quick-start)
* [Guide](https://github.com/terox/SubscriptionBundle/blob/master/doc/Guide.md)
* Strategies
    * Product strategies:
        * [What is a product strategy](https://github.com/terox/SubscriptionBundle/blob/master/doc/WhatIsProductStrategy.md)
        * [How to create a product strategy](https://github.com/terox/SubscriptionBundle/blob/master/doc/HowToCreateAProductStrategy.md])
        * [Out-of-the-box strategies](https://github.com/terox/SubscriptionBundle/blob/master/doc/strategies/product):
            * [Default product strategy](https://github.com/terox/SubscriptionBundle/blob/master/doc/strategies/product/DefaultStrategy.md)
            
    * Subscription strategies:
        * [What is a subscription strategy](https://github.com/terox/SubscriptionBundle/blob/master/doc/WhatIsAProductStrategy.md)
        * [How to create a subscription strategy](https://github.com/terox/SubscriptionBundle/blob/master/doc/HowToCreateASubscriptionStrategy.md)
        * [Out-of-the-box strategies](https://github.com/terox/SubscriptionBundle/blob/master/doc/strategies/subscription):
            * [End Last Strategy](https://github.com/terox/SubscriptionBundle/blob/master/doc/strategies/subscription/EndLastStrategy.md)

* CookBooks/Examples:
    * [Symfony 4 example sandbox](https://github.com/terox/sf4-subscription-example) with doctrine

Quick start
-----------

### 1. Download the bundle:

```bash
$ composer require terox/subscription-bundle
```

### 2. Enable the bundle in Symfony Application (only Symfony 3):

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
```
Read the [complete configuration reference](https://github.com/terox/SubscriptionBundle/blob/master/doc/ReferenceConfig.md) for more configuration options or tweaks.

License
-------

This software is published under the [MIT License](https://github.com/terox/SubscriptionBundle/master/LICENSE.md)

Contributing
------------

I will be very happy if you want to contribute fixing some issue, providing new strategies or whatever you want. Thanks!
