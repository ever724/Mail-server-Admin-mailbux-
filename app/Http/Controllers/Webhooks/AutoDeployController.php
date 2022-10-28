<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class AutoDeployController extends Controller
{
    const RC_REPOSITORY_NAME = 'ravgrg/mb-inbox';
    const ADMIN_REPOSITORY_NAME = 'alliuqemal/adminpanel';

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deploy(Request $request): JsonResponse
    {
        $repositoryName = $request->input('repository.full_name');

        switch ($repositoryName) {
            case self::RC_REPOSITORY_NAME:
                $process = new Process(['/var/www/html/deploy_script.sh']);
                $process->run();

                return response()->json([
                    'success' => $process->isSuccessful(),
                    'output' => $process->getOutput(),
                    'errors' => $process->getErrorOutput(),
                ]);

            case self::ADMIN_REPOSITORY_NAME:
                $process = new Process(['/var/www/html/secure/deploy.sh']);
                $process->run();

                return response()->json([
                    'success' => $process->isSuccessful(),
                    'output' => $process->getOutput(),
                    'errors' => $process->getErrorOutput(),
                ]);
        }

        return response()->json([
            'success' => false,
        ], 500);
    }
}
