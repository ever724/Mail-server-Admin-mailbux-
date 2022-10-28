<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\MailbuxService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $clientId;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $clientId
    ) {
        $this->clientId = $clientId;
    }

    /**
     * Execute the job.
     *
     * @param MailbuxService $mailbuxService
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function handle(MailbuxService $mailbuxService): bool
    {
        $client = Client::query()->find($this->clientId);

        if (!$client instanceof Client) {
            return false;
        }

        if ($client->exists_on_mail_server) {
            $deletedFromServer = $mailbuxService->deleteUser($client->email, $error);
        }

        if ($client->exists_on_mail_server && !$deletedFromServer) {
            if (!is_null($error)) {
                throw new \Exception($error);
            }

            return false;
        }
        $client->exists_on_mail_server = false;
        $client->save();

        $deleted = DB::connection('inbox')
            ->table('users')
            ->where('username', '=', $client->email)
            ->delete();

        if ($deleted) {
            return $client->delete();
        }

        return false;
    }
}
