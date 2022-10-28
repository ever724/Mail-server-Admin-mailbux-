<?php

namespace App\Services\Gateways;

use Omnipay\Omnipay;

class Stripe
{
    public $company;

    /**
     * PaypalExpress Construct.
     *
     * @param mixed $company
     * @param mixed $saas
     */
    public function __construct($company, $saas = false)
    {
        $this->saas = $saas;
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function gateway()
    {
        $gateway = Omnipay::create('Stripe\PaymentIntents');

        if ($this->saas) {
            $gateway->setApiKey(get_system_setting('stripe_secret_key'));
            $gateway->setTestMode(get_system_setting('stripe_test_mode'));

            return $gateway;
        }

        $gateway->setApiKey($this->company->getSetting('stripe_secret_key'));
        $gateway->setTestMode($this->company->getSetting('stripe_test_mode'));

        return $gateway;
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function purchase(array $parameters)
    {
        return $this->gateway()
            ->purchase($parameters)
            ->send();
    }

    /**
     * @param array $parameters
     */
    public function complete(array $parameters)
    {
        return $this->gateway()
            ->confirm($parameters)
            ->send();
    }

    /**
     * @param $amount
     */
    public function formatAmount($amount)
    {
        return number_format($amount / 100, 2, '.', '');
    }

    /**
     * @param $invoice
     */
    public function getReturnUrl($invoice)
    {
        return route('customer_portal.invoices.stripe.completed', [
            'customer' => $invoice->customer->uid,
            'invoice' => $invoice->uid,
        ]);
    }
}
