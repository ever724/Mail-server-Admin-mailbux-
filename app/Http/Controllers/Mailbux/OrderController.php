<?php

namespace App\Http\Controllers\Mailbux;

use App\Console\Commands\UpdateSubscriptionsMetaData;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Models\SystemSetting;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Client $client
     * @param int    $plan_id
     *
     * @return Application|Redirector|RedirectResponse|string
     */
    public function index(Client $client, int $plan_id)
    {
        if (!SystemSetting::isPaddleActive()) {
            return 'Paddle is not activated';
        }

        $plan = Plan::query()
            ->where('paddle_annual_id', $plan_id)
            ->orWhere('paddle_monthly_id', $plan_id)
            ->firstOrFail();

        $isMonthly = true;

        if ($plan_id == $plan->paddle_annual_id) {
            $isMonthly = false;
        }

        return view(
            'application.paddle.checkout',
            get_defined_vars()
        );
    }

    /**
     * Paddle Complete Payment.
     *
     * @param Request $request
     *
     * @throws GuzzleException
     *
     * @return Application|Redirector|RedirectResponse
     */
    public function paddle_completed(Request $request)
    {
        // payment complete
        return redirect();
    }

    public function update(Client $client, PlanSubscription $subscription)
    {
        $update_url = $subscription->meta_data['update_url'] ?? null;
        $success_url = route('mailbux.payment.update.success', [$client, $subscription]);

        return view('application.paddle.update', [
            'update_url' => $update_url,
            'subscription' => $subscription,
            'plan' => $subscription->plan,
            'client' => $client,
            'success_url' => $success_url,
        ]);
    }

    /**
     * Cancel the subscription.
     *
     * @param Client           $client
     * @param PlanSubscription $subscription
     *
     * @return Application|Factory|RedirectResponse|View
     */
    public function cancel(Client $client, PlanSubscription $subscription)
    {
        $cancel_url = $subscription->meta_data['cancel_url'] ?? null;
        $success_url = route('payment.subscription.cancel.success', [$client, $subscription]);

        if ($cancel_url) {
            return view('application.paddle.cancel', [
                'cancel_url' => $cancel_url,
                'success_url' => $success_url,
                'client' => $client,
            ]);
        }

        session()->flash('alert-error', __('messages.cannot_cancel'));

        return redirect()->route('super_admin.subscriptions');
    }

    /**
     * @param Client           $client
     * @param PlanSubscription $subscription
     *
     * @return RedirectResponse
     */
    public function afterUpdate(Client $client, PlanSubscription $subscription): RedirectResponse
    {
        UpdateSubscriptionsMetaData::updateMetaData($subscription);

        return redirect()->route('super_admin.subscriptions');
    }

    /**
     * @param Client           $client
     * @param PlanSubscription $subscription
     *
     * @return RedirectResponse
     */
    public function afterCancel(Client $client, PlanSubscription $subscription): RedirectResponse
    {
        $subscription->cancel(true);

        session()->flash('alert-success', __('messages.subscription_cancelled'));

        return redirect()->route('super_admin.subscriptions');
    }
}
