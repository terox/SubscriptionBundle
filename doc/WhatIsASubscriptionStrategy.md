What is a subscription strategy
===============================

A **subscription strategy** is the core. The subscription strategy controls how a new subscription should be created. It 
handles the flow between current active subscriptions and new incoming subscriptions. It try to answer some questions
like:

* Should create a new subscription if the user has some active?
* Should allow permanent subscriptions (without any end date)?
* What happens if the user has a permanent subscription active?
* And more related questions...

The bundle comes with **[end_last](https://github.com/terox/SubscriptionBundle/blob/master/doc/strategies/subscription/EndLast.md)**
strategy by default. It handle the most common cases, but you can create you own strategy [following this guide](https://github.com/terox/SubscriptionBundle/blob/master/doc/HowToCreateASubscriptionStrategy.md).