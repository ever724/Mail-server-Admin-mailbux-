<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(title="API V1", version="0.1")
 */
abstract class BaseController extends Controller
{
    /**
     * @return string
     */
    abstract protected function resource(): string;

    /**
     * Response.
     *
     * @param mixed $result
     * @param mixed $success
     * @param mixed $status
     * @param mixed $data
     */
    public function sendCollectionResponse($result, $success = true, $status = 200, $data = [])
    {
        return ($this->resource()::collection($result))
            ->additional(array_merge(['success' => $success], $data))
            ->response()
            ->setStatusCode($status);
    }

    /**
     * Response.
     *
     * @param mixed $result
     * @param mixed $success
     * @param mixed $status
     * @param mixed $data
     */
    public function sendResponse($result, $success = true, $status = 200, $data = [])
    {
        if ($result instanceof \Illuminate\Database\Eloquent\Model) {
            $resource = $this->resource();

            return (new $resource($result))
                ->additional(array_merge(['success' => $success], $data))
                ->response()
                ->setStatusCode($status);
        }

        return response()->json(array_merge([
            'data' => [],
            'success' => $success,
        ], $data))->setStatusCode($status);
    }
}
