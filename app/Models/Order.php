<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'subscription_id',
        'subscription_order_id',
        'company_id',
        'client_id',
        'plan_id',
        'card_number',
        'card_exp_month',
        'card_exp_year',
        'price',
        'formatted_tax',
        'formatted_total',
        'total_tax',
        'currency',
        'transaction_id',
        'payment_type',
        'payment_status',
        'receipt',
        'complete_timezone',
        'completed_date',
        'billing_interval',
    ];

    /**
     * Define Relation with Company Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Define Relation with Plan Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
