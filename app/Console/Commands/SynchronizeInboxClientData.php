<?php

namespace App\Console\Commands;

use App\External\MailbuxServer\References;
use App\Models\Client;
use App\Services\InboxService;
use App\Services\MailbuxService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SynchronizeInboxClientData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailbux:roundcube:sync:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Clients from Roundcube System';

    /**
     * @var MailbuxService
     */
    private $mailbuxService;

    /**
     * @var InboxService
     */
    private $inboxService;

    /**
     * Create a new command instance.
     */
    public function __construct(MailbuxService $mailbuxService, InboxService $inboxService)
    {
        parent::__construct();

        $this->mailbuxService = $mailbuxService;
        $this->inboxService = $inboxService;
    }

    /**
     * @throws GuzzleException
     */
    public function handle()
    {
        $mailServerUsers = collect(
            $this->mailbuxService->getUsers()
        )->keyBy('username');

        $this->storeNewMailServerUsersToInbox($mailServerUsers);

        $this->syncClientsWithInbox($mailServerUsers);

        $this->deleteOldInboxUsers();
    }

    /**
     * @param Collection $mailServerUsers
     */
    private function storeNewMailServerUsersToInbox(Collection $mailServerUsers): void
    {
        $storedToClient = 0;

        $existing = $this->inboxService->getAllInboxClients()->pluck('username')->flip();

        $mailServerUsers->each(function ($user) use ($existing, &$storedToClient) {
            $email = $user['username'];

            if ($user['perm_level'] !== References::PERMISSION_LEVEL_MAIL_USER) {
                return;
            }

            if ($existing->has($email)) {
                return;
            }

            $userId = $this->inboxService->storeClient(
                $email,
                '',
                $this->mailbuxService->getMailHost(),
                $user['language'],
                [],
                null,
                null
            );

            if ($userId) {
                $storedToClient++;
            }
        });
    }

    /**
     * @param Collection $mailServerUsers
     */
    private function syncClientsWithInbox(Collection $mailServerUsers): void
    {
        $lastSyncAt = now();

        $this->inboxService
            ->getAllInboxClients()
            ->groupBy(function ($user) {
                return $user->id;
            })->each(function (Collection $users, $user_id) use ($lastSyncAt, $mailServerUsers) {
                $latestRecord = $users->sortByDesc(function ($user) {
                    return $user->changed;
                })->first();

                $email = $latestRecord->email ?? $latestRecord->username ?? '';

                $existsOnMailServer = !empty($mailServerUsers[$email]);

                $data = [
                    'api_access' => false,
                    'enabled' => false,
                    'domain' => null,
                    'language' => 'en',
                    'last_login' => null,
                    'storagequota_total' => 0,
                    'storagequota_used' => 0,
                ];

                if ($existsOnMailServer) {
                    $recoveryEmail = $mailServerUsers[$email]['recovery_email'];

                    foreach ($mailServerUsers[$email] as $key => $value) {
                        $data[$key] = $value;
                    }
                }

                Client::query()
                    ->updateOrCreate(
                        ['id' => $user_id],
                        [
                            'name' => $latestRecord->name ?? '',
                            'organization' => $latestRecord->organization ?? '',
                            'last_synced_at' => $lastSyncAt,
                            'email' => $email,
                            'exists_on_mail_server' => $existsOnMailServer,
                            'recovery_email' => $recoveryEmail ?? null,
                            'api_access' => $data['api_access'],
                            'enabled' => $data['enabled'],
                            'domain' => $data['domain'],
                            'language' => $data['language'],
                            'last_login' => isset($data['last_login']) ? Carbon::createFromTimestamp($data['last_login']) : null,
                            'storagequota_total' => $data['storagequota_total'],
                            'storagequota_used' => $data['storagequota_used'],
                            'uid' => uniqid(),
                        ]
                    );
            });
    }

    private function deleteOldInboxUsers(): void
    {
        $existing = $this->inboxService->getAllInboxClients()->pluck('user_id');

        Client::query()->whereNotIn('id', $existing)->delete();
    }
}
