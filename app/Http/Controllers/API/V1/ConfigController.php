<?php

namespace App\Http\Controllers\API\V1;

use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;

class ConfigController
{
    /**
     * @param string $key
     *
     * @return JsonResponse
     */
    public function getConfigValue(string $key): JsonResponse
    {
        $configValue = SystemSetting::getSetting($key);

        if ($configValue) {
            return response()->json([
                'key' => $key,
                'value' => $configValue,
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => [
                'value not found',
            ],
        ]);
    }
}
