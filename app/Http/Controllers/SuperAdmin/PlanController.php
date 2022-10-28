<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Plan\Store;
use App\Http\Requests\SuperAdmin\Plan\Update;
use App\Models\Plan;
use App\Models\SystemSetting;
use App\Services\PaddleService;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    private $paddle;

    public function __construct(PaddleService $paddle)
    {
        $this->paddle = $paddle;
    }

    /**
     * Display Super Admin Plans Page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get Plans
        $plans = QueryBuilder::for(Plan::class)
            ->orderBy('order', 'ASC')
            ->paginate()
            ->appends(request()->query());

        return view('super_admin.plans.index', [
            'plans' => $plans,
        ]);
    }

    /**
     * Display the Form for Creating New Plan.
     */
    public function create(Request $request)
    {
        $copyFrom = $request->input('clone');

        if ($copyFrom) {
            /** @var Plan $plan */
            $plan = Plan::query()->findOrFail($copyFrom);

            $info = Arr::only($plan->toArray(), [
                'name',
                'description',
                'monthly_price',
                'monthly_sales_price',
                'annual_price',
                'annual_sales_price',
                'trial_period',
                'mailbux_settings',
            ]);

            $info['name'] = $info['name'] . ' - Copy';

            $features = $plan->features->map(function ($feature) {
                return Arr::only($feature->toArray(), [
                    'label',
                    'value',
                    'is_displayed',
                ]);
            });
        } else {
            $info = [];
            $features = [];
        }

        if (!SystemSetting::isPaddleActive()) {
            session()->flash('alert-danger', 'Paddle not activated.');

            return redirect()->route('super_admin.plans');
        }

        $plan = new Plan();
        $plan->fill($info);

        [$monthlyPaddlePlans, $annualPaddlePlans] = $this->getAvailablePaddlePlans($plan);

        return view('super_admin.plans.create', [
            'plan' => $plan,
            'monthlyPaddlePlans' => $monthlyPaddlePlans,
            'annualPaddlePlans' => $annualPaddlePlans,
            'features' => $features,
        ]);
    }

    /**
     * Store the plan in Database.
     *
     * @param \App\Http\Requests\SuperAdmin\Plan\Store $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request)
    {
        $int = '';
        do {
            $slug = Str::slug($request->name, '-') . $int;
            if ($int == '') {
                $int = 1;
            } else {
                $int++;
            }
        } while (Plan::where('slug', $slug)->exists());

        $plan = new Plan();

        // Create new Plan
        $plan->fill([
            'slug' => $slug,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
            'price' => $request->price,
            'invoice_period' => 1,
            'trial_period' => $request->trial_period, // trial days
            'trial_interval' => 'day',
            'order' => $request->order ?? 0,
            'paddle_id' => $request->paddle_id,
            'annual_price' => $request->annual_price,
            'annual_sales_price' => $request->annual_sales_price,
            'monthly_price' => $request->monthly_price,
            'monthly_sales_price' => $request->monthly_sales_price,
            'mailbux_settings' => $request->mailbux_settings,
        ]);
        $plan->save();

        // Create new Plan Features
        $plan->updatePlanFeatures($request->features);

        session()->flash('alert-success', __('messages.plan_created'));

        return redirect()->route('super_admin.plans');
    }

    /**
     * Display the Form for Editing Plan.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        if (!SystemSetting::isPaddleActive()) {
            session()->flash('alert-danger', 'Paddle not activated.');

            return redirect()->route('super_admin.plans');
        }
        $plan = Plan::findOrFail($request->plan);

        [$monthlyPaddlePlans, $annualPaddlePlans] = $this->getAvailablePaddlePlans($plan);

        // Fill model with old input
        if (!empty($request->old())) {
            $plan->fill($request->old());
        }

        return view('super_admin.plans.edit', [
            'plan' => $plan,
            'monthlyPaddlePlans' => $monthlyPaddlePlans,
            'annualPaddlePlans' => $annualPaddlePlans,
        ]);
    }

    /**
     * Update the Package in Database.
     *
     * @param \App\Http\Requests\SuperAdmin\Plan\Update $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Update $request, Plan $plan)
    {
        // Update the Plan
        $plan->update($request->validated());

        // Create new Plan Features
        $plan->updatePlanFeatures($request->features);

        session()->flash('alert-success', __('messages.plan_updated'));

        return redirect()->route('super_admin.plans.edit', $plan->id);
    }

    /**
     * Delete the Package.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(Request $request)
    {
        $plan = Plan::findOrFail($request->plan);

        // Delete Plan's Features from Database
        if ($plan->features()->exists()) {
            $plan->features()->delete();
        }

        // Delete Plan's Subscriptions from Database
        if ($plan->subscriptions()->exists()) {
            $plan->subscriptions()->delete();
        }

        // Delete plan
        $plan->delete();

        session()->flash('alert-success', __('messages.plan_deleted'));

        return redirect()->route('super_admin.plans');
    }

    /**
     * @param Plan $currentPlan
     *
     * @return array
     */
    private function getAvailablePaddlePlans(Plan $currentPlan): array
    {
        $paddlePlans = $this->paddle->getPlans();

        $takenMonthlyPlans = Plan::query()->pluck('paddle_monthly_id')->toArray();
        $takenAnnualPlans = Plan::query()->pluck('paddle_annual_id')->toArray();

        $monthlyPaddlePlans = collect($paddlePlans)
            ->filter(function ($paddlePlan) use ($currentPlan, $takenMonthlyPlans) {
                return
                    $paddlePlan['billing_type'] == 'month'
                    && (
                        $paddlePlan['id'] == $currentPlan->paddle_monthly_id
                        || !in_array($paddlePlan['id'], $takenMonthlyPlans)
                    );
            });

        $annualPaddlePlans = collect($paddlePlans)
            ->filter(function ($paddlePlan) use ($currentPlan, $takenAnnualPlans) {
                return $paddlePlan['billing_type'] == 'year'
                    && (
                        $paddlePlan['id'] == $currentPlan->paddle_annual_id
                        || !in_array($paddlePlan['id'], $takenAnnualPlans)
                    );
            });

        return [$monthlyPaddlePlans, $annualPaddlePlans];
    }
}
