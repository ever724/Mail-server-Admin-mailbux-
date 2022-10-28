<?php

namespace App\Listeners;

use App\External\Paddle\Handlers\PaddleWebhookHandlerInterface;
use App\External\Paddle\Handlers\SubscriptionCreatedHandler;
use App\External\Paddle\Handlers\SubscriptionPaidHandler;
use App\External\Paddle\Handlers\SubscriptionPaymentFailedHandler;
use App\External\Paddle\References;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Laravel\Paddle\Events\WebhookReceived;

class PaddleEventListener
{
    private $handlers = [
        References::EVENT_SUBSCRIPTION_PAYMENT_SUCCESS => SubscriptionPaidHandler::class,
        References::EVENT_SUBSCRIPTION_PAYMENT_FAILED => SubscriptionPaymentFailedHandler::class,
        References::EVENT_SUBSCRIPTION_CREATED => SubscriptionCreatedHandler::class,
    ];

    /**
     * Handle the event.
     *
     * @param WebhookReceived $event
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(WebhookReceived $event)
    {
        $payload = $event->payload;

        $alertName = Arr::get($payload, 'alert_name');
        $handler = $this->makeHandler($alertName);
        $result = $handler->handle($payload);

        $this->throwIfResponse($result);
    }

    /**
     * @param $alertName
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return null|PaddleWebhookHandlerInterface
     */
    private function makeHandler($alertName): ?PaddleWebhookHandlerInterface
    {
        $handlerClass = $this->handlers[$alertName] ?? null;

        if ($handlerClass) {
            return app()->make($handlerClass);
        }

        return null;
    }

    private function throwIfResponse($result)
    {
        if ($result instanceof JsonResponse) {
            throw new HttpResponseException($result);
        }
    }
}
