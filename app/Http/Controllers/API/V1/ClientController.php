<?php

namespace App\Http\Controllers\API\V1;

use App\Console\Commands\SynchronizeInboxClientData;
use App\Http\Resources\ClientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class ClientController extends BaseController
{
    /**
     * @return string
     */
    protected function resource(): string
    {
        return ClientResource::class;
    }

    /**
     * @return JsonResponse
     */
    public function sync(): JsonResponse
    {
        Artisan::call(SynchronizeInboxClientData::class);

        return response()->json([
            'message' => __('messages.sync_successful'),
        ]);
    }
}
