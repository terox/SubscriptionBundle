<?php

namespace Terox\SubscriptionBundle\Event;

final class SubscriptionEvents
{
    /**
     * Activate subscription.
     *
     * Triggered when a subscription is activated.
     */
    const ACTIVATE_SUBSCRIPTION = 'terox.subscription.activate';

    /**
     * Renew a subscription.
     *
     * Triggered when subscription is renewed.
     */
    const RENEW_SUBSCRIPTION = 'terox.subscription.renew';

    /**
     * Expire subscription.
     *
     * Triggered when subscription is expired.
     */
    const EXPIRE_SUBSCRIPTION = 'terox.subscription.expire';

    /**
     * Disable subscription.
     *
     * Triggered when on-demand subscription is disabled.
     */
    const DISABLE_SUBSCRIPTION = 'terox.subscription.disable';
}