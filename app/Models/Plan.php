<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'annual_price',
        'monthly_price',
        'annual_sales_price',
        'monthly_sales_price',
        'trial_period',
        'trial_interval',
        'invoice_period',
        'grace_period',
        'grace_interval',
        'prorate_day',
        'prorate_period',
        'prorate_extend_due',
        'active_subscribers_limit',
        'order',
        'paddle_annual_id',
        'paddle_monthly_id',
        'mailbux_settings',
    ];

    /**
     * Automatically cast attributes to given types.
     *
     * @var array
     */
    protected $casts = [
        'slug' => 'string',
        'is_active' => 'boolean',
        'annual_sales_price' => 'integer',
        'monthly_sales_price' => 'integer',
        'trial_period' => 'integer',
        'trial_interval' => 'string',
        'invoice_period' => 'integer',
        'grace_period' => 'integer',
        'grace_interval' => 'string',
        'prorate_day' => 'integer',
        'prorate_period' => 'integer',
        'prorate_extend_due' => 'integer',
        'active_subscribers_limit' => 'integer',
        'deleted_at' => 'datetime',
        'order' => 'integer',
        'paddle_annual_id' => 'integer',
        'paddle_monthly_id' => 'integer',
    ];

    protected $appends = [
        'is_free',
    ];

    /**
     * The plan may have many features.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class, 'plan_id', 'id')->orderBy('order');
    }

    /**
     * The plan may have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(PlanSubscription::class, 'plan_id', 'id');
    }

    /**
     * Create Plan Features.
     *
     * @param mixed $features
     */
    public function addPlanFeatures($features): void
    {
        // Create new Plan Features
        foreach ($features as $feature => $value) {
            $slug = Str::slug($value['label'], '-');
            $this->features()->create([
                'slug' => $slug,
                'value' => $value['value'],
                'label' => ucfirst($value['label']),
                'is_displayed' => $value['is_displayed'],
                'order' => $value['order'],
            ]);
        }
    }

    /**
     * Update Plan Features.
     *
     * @param $features
     */
    public function updatePlanFeatures($features): void
    {
        $ids = $this->features()->pluck('id')->flip()->toArray();

        foreach ($features as $feature) {
            $slug = Str::slug($feature['label'], '-');

            $data = [
                'plan_id' => $this->id,
                'slug' => $slug,
                'value' => $feature['value'],
                'label' => ucfirst($feature['label']),
                'is_displayed' => $feature['is_displayed'],
                'order' => $feature['order'],
            ];

            if (isset($feature['id'])) {
                $id = (int) $feature['id'];
                unset($ids[$id]);

                $this->features()
                    ->where('id', $id)
                    ->update($data);
            } else {
                $this->features()->create($data);
            }
        }

        $this->features()->whereIn('id', array_keys($ids))->delete();
    }

    /**
     * Get Currency Attribute.
     */
    public function getCurrencyAttribute()
    {
        return get_system_setting('application_currency');
    }

    /**
     * Check if plan is free.
     *
     * @return bool
     */
    public function getIsFreeAttribute(): bool
    {
        return (int) $this->annual_price === 0 && (int) $this->monthly_price === 0;
    }

    /**
     * Check if plan has trial.
     *
     * @return bool
     */
    public function hasTrial(): bool
    {
        return $this->trial_period && $this->trial_interval;
    }

    /**
     * Check if plan has grace.
     *
     * @return bool
     */
    public function hasGrace(): bool
    {
        return $this->grace_period && $this->grace_interval;
    }

    /**
     * Get plan feature by the given slug.
     *
     * @param string $featureSlug
     *
     * @return null|\App\Models\PlanFeature
     */
    public function getFeatureBySlug(string $featureSlug)
    {
        return $this->features()->where('slug', $featureSlug)->first();
    }

    /**
     * Activate the plan.
     *
     * @return $this
     */
    public function activate()
    {
        $this->update(['is_active' => true]);

        return $this;
    }

    /**
     * Deactivate the plan.
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);

        return $this;
    }

    /**
     * List Plans for Select2 Javascript Library.
     *
     * @return collect
     */
    public static function getSelect2Array()
    {
        $response = collect();
        foreach (self::all() as $plan) {
            $response->push([
                'id' => $plan->id,
                'text' => $plan->name,
            ]);
        }

        return $response;
    }

    public function active_features()
    {
        return $this->features()->where('is_displayed', true);
    }

    /**
     * @param string $value
     *
     * @return array
     */
    public function getMailbuxSettingsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return json_decode($value, true) ?? [];
    }

    /**
     * @param array $value
     */
    public function setMailbuxSettingsAttribute(array $value)
    {
        $this->attributes['mailbux_settings'] = json_encode($value);
    }
}
