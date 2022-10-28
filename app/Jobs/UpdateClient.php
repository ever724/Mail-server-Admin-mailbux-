<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\InboxService;
use App\Services\MailbuxService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class UpdateClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $organization;

    /**
     * @var null|string
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
        Client $client,
        string $name,
        string $organization,
        ?string $password,
        bool $apiAccess,
        bool $enabled,
        string $recoveryEmail,
        string $language,
        int $storageQuotaTotal
    ) {
        $this->client = $client;
        $this->name = $name;
        $this->organization = $organization;
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
    public function handle(
        InboxService $inboxService,
        MailbuxService $mailbuxService
    ) {
        $mailbuxService->updateMailUser(
            $this->client->email,
            $this->password,
            $this->apiAccess,
            $this->enabled,
            $this->recoveryEmail,
            $this->language,
            $this->storageQuotaTotal
        );

        $inboxService->updateClient(
            $this->client->getKey(),
            $this->client->email,
            $this->password,
            $mailbuxService->getMailHost(),
            $this->language,
            $this->name,
            $this->organization
        );

        $updatedData = [
            'name' => $this->name,
            'api_access' => $this->apiAccess,
            'enabled' => $this->enabled,
            'organization' => $this->organization,
            'recovery_email' => $this->recoveryEmail,
            'storagequota_total' => $this->storageQuotaTotal,
        ];

        if (!is_null($this->password)) {
            $updatedData['password'] = Hash::make($this->password);
        }

        $this->client->update($updatedData);
    }
}
