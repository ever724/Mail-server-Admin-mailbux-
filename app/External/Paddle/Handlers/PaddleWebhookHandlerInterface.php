<?php

namespace App\External\Paddle\Handlers;

interface PaddleWebhookHandlerInterface
{
    /**
     * @param array $payload
     *
     * @return mixed
     */
    public function handle(array $payload);
}
