<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'annual_price' => $this->annual_price,
            'monthly_price' => $this->monthly_price,
            'annual_sales_price' => $this->annual_sales_price,
            'monthly_sales_price' => $this->monthly_sales_price,
            'trial_period' => $this->trial_period,
            'trial_interval' => $this->trial_interval,
            'order' => $this->order,
            'paddle_annual_id' => $this->paddle_annual_id,
            'paddle_monthly_id' => $this->paddle_monthly_id,
            'is_free' => $this->is_free,
            'features' => PlanFeatureResource::collection($this->whenLoaded('active_features')),
            'monthly_purchase_link' => $this->when(
                $this->paddle_monthly_id,
                route('mailbux.payment.start', [$request->client->id, $this->paddle_monthly_id ?? 0]),
                __('messages.unavailable')
            ),
            'annual_purchase_link' => $this->when(
                $this->paddle_annual_id,
                route('mailbux.payment.start', [$request->client->id, $this->paddle_annual_id ?? 0]),
                __('messages.unavailable')
            ),
            'is_current' => $request->client->plan_subscriptions()->where('plan_id', $this->id)->exists() ||
                ($request->client->plan_subscriptions()->count() == 0 && $this->is_free),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
