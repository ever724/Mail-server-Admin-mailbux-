<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionInvoice extends Model
{
    const STATUS_TRIAL = 'trialing';
    const STATUS_ACTIVE = 'active';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_TRIAL,
    ];

    const PAYMENT_STATUS_SUCCESS = 1;
    const PAYMENT_STATUS_UNPAID = 2;
    const PAYMENT_STATUS_REFUNDED = 3;

    protected $fillable = [
        'order_number',
        'client_id',
        'amount',
        'currency',
        'country',
        'paid_at',
        'payment_status',
        'next_payment_date',
        'next_payment_amount',
        'is_first_payment',
        'payment_method',
        'status',
        'paddle_subscription_id',
        'paddle_plan_id',
        'paddle_checkout_id',
        'response_data',
    ];

    protected $dates = [
        'paid_at',
        'next_payment_date',
    ];

    protected $casts = [
        'next_payment_date' => 'date:Y-m-d',
    ];

    /**
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'paddle_plan_id', 'paddle_id');
    }

    /**
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(PlanSubscription::class, 'paddle_subscription_id', 'subscription_id');
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * @param array $payload
     */
    public function setResponseDataAttribute(array $payload)
    {
        $this->attributes['response_data'] = serialize($payload);
    }

    /**
     * @return array
     */
    public function getResponseDataAttribute(): array
    {
        $data = $this->original['response_data'];

        return (array) unserialize($data);
    }
}
