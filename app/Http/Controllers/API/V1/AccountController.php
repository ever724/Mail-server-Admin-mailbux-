<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Application\Settings\Account\UpdateAPI as AccountUpdate;
use App\Http\Requests\Application\Settings\Notification\Update as NotificationUpdate;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AccountController extends BaseController
{
    // Resource
    public $resource = UserResource::class;

    /**
     * Get the authenticated user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        $user = $request->user();

        return $this->sendResponse($user, true, 200);
    }

    /**
     * Update the authenticated user's settings.
     *
     * @param AccountUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function settings(AccountUpdate $request)
    {
        $user = $request->user();
        $user->updateModel($request);

        return $this->sendResponse($user, true, 200, [
            'message' => __('messages.account_updated'),
        ]);
    }

    /**
     * Update the authenticated user's settings.
     *
     * @param NotificationUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notification(NotificationUpdate $request)
    {
        // Update each settings in database
        foreach ($request->validated() as $key => $value) {
            $request->user()->setSetting($key, $value);
        }

        return $this->sendResponse($request->user(), true, 200, [
            'message' => __('messages.notification_settings_updated'),
        ]);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return $this->resource;
    }
}
