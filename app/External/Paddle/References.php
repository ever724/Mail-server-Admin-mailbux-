<?php

namespace App\External\Paddle;

interface References
{
    const EVENT_SUBSCRIPTION_PAYMENT_SUCCESS = 'subscription_payment_succeeded';
    const EVENT_SUBSCRIPTION_PAYMENT_FAILED = 'subscription_payment_failed';
    const EVENT_SUBSCRIPTION_PAYMENT_REFUNDED = 'subscription_payment_refunded';

    const EVENT_SUBSCRIPTION_CREATED = 'subscription_created';
    const EVENT_SUBSCRIPTION_UPDATED = 'subscription_updated';
    const EVENT_SUBSCRIPTION_CANCELLED_OR_ENDED = 'subscription_cancelled';

    const URI_SUBSCRIPTION_USERS = '/subscription/users';
    const URI_SUBSCRIPTION_PLANS = '/subscription/plans';
}
