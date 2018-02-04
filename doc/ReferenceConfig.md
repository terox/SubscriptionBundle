Configuration Reference
-----------------------

```yaml
terox_subscription:
    # Where is the subscription model located in your application
    # Remember that your model must implement the interface
    subscription_class: AppBundle\Entity\Subscription # Interface: Terox\SubscriptionBundle\Model\SubscriptionInterface

    # Repository services name
    # Remember that repositories must be implement the interfaces
    subscription_repository: app.repository.subscription # Interface: Terox\SubscriptionBundle\Repository\SubscriptionRepositoryInterface
    product_repository: app.repository.product           # Interface: Terox\SubscriptionBundle\Repository\ProductRepositoryInterface
    
    # You can change the default message when a subscription change his state
    reasons:
        renew:   'Subscription expired and auto-renewal'
        expire:  'Subscription expired'
        disable: 'Subscription disabled'
```