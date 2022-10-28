<?php

namespace App\Jobs;

use App\External\MailbuxServer\References;
use App\Models\Client;
use App\Services\InboxService;
use App\Services\MailbuxService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMailUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $name;

    /**
     * @var null|string
     */
    private $organization;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $apiAccess;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $recoveryEmail;

    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $storageQuotaTotal;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $name,
        ?string $organization,
        string $domain,
        string $username,
        string $password,
        bool $apiAccess,
        bool $enabled,
        string $recoveryEmail,
        string $language = 'en',
        int $storageQuotaTotal = 100
    ) {
        $this->name = $name;
        $this->organization = $organization;
        $this->domain = $domain;
        $this->username = $username;
        $this->password = $password;
        $this->apiAccess = $apiAccess;
        $this->enabled = $enabled;
        $this->recoveryEmail = $recoveryEmail;
        $this->language = $language;
        $this->storageQuotaTotal = $storageQuotaTotal;
    }

    /**
     * Execute the job.
     *
     * @throws GuzzleException
     */
    public function handle(MailbuxService $mailbuxService, InboxService $inboxService)
    {
        $mailUser = $mailbuxService->storeUser(
            $this->username,
            $this->password,
            $this->recoveryEmail,
            References::PERMISSION_LEVEL_MAIL_USER,
            $this->apiAccess,
            $this->enabled,
            [
                'storagequota_total' => $this->storageQuotaTotal,
                'domain' => $this->domain,
            ]
        );

        $userId = $inboxService->storeClient(
            $mailUser['username'],
            null,
            $mailbuxService->getMailHost(),
            $mailUser['language'],
            [],
            $this->name,
            $this->organization
        );

        return Client::query()->create([
            'id' => $userId,
            'uid' => uniqid(),
            'name' => $this->name,
            'organization' => $this->organization,
            'email' => $mailUser['username'],
            'exists_on_mail_server' => true,
            'recovery_email' => $mailUser['recovery_email'],
            'api_access' => $mailUser['api_access'],
            'enabled' => $mailUser['enabled'],
            'domain' => $mailUser['domain'],
            'language' => $mailUser['language'],
            'last_login' => null,
            'storagequota_total' => $mailUser['storagequota_total'],
            'storagequota_used' => $mailUser['storagequota_used'] ?? 0,
            'last_synced_at' => now(),
        ]);
    }
}
