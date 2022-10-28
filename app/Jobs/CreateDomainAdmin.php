<?php

namespace App\Jobs;

use App\Services\MailbuxService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateDomainAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $recovery_email;

    /**
     * @var string
     */
    private $perm_level;

    /**
     * @var bool
     */
    private $api_access;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $mailbuxSettings;

    /**
     * @var array
     */
    private $domains;

    /**
     * @var int
     */
    private $storagequota_total;

    /**
     * @var int
     */
    private $quota_domains;

    /**
     * @var int
     */
    private $quota_mailboxes;

    /**
     * @var int
     */
    private $quota_aliases;

    /**
     * @var int
     */
    private $quota_domainaliases;

    /**
     * Create a new job instance.
     *
     * @throws GuzzleException // thrown on handler
     */
    public function __construct(
        string $email,
        string $password,
        string $recovery_email,
        string $perm_level,
        bool $api_access,
        bool $enabled,
        array $domains,
        int $storagequota_total,
        int $quota_domains,
        int $quota_mailboxes,
        int $quota_aliases,
        int $quota_domainaliases
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->recovery_email = $recovery_email;
        $this->perm_level = $perm_level;
        $this->api_access = $api_access;
        $this->enabled = $enabled;
        $this->domains = $domains;
        $this->storagequota_total = $storagequota_total;
        $this->quota_domains = $quota_domains;
        $this->quota_mailboxes = $quota_mailboxes;
        $this->quota_aliases = $quota_aliases;
        $this->quota_domainaliases = $quota_domainaliases;
    }

    /**
     * Execute the job.
     *
     * @throws GuzzleException
     */
    public function handle(MailbuxService $mailbuxService): ?array
    {
        return $mailbuxService->storeUser(
            $this->email,
            $this->password,
            $this->recovery_email,
            $this->perm_level,
            $this->api_access,
            $this->enabled,
            [
                'domains' => implode(', ', $this->domains),
                'storagequota_total' => $this->storagequota_total,
                'quota_domains' => $this->quota_domains,
                'quota_mailboxes' => $this->quota_mailboxes,
                'quota_aliases' => $this->quota_aliases,
                'quota_domainaliases' => $this->quota_domainaliases,
            ]
        );
    }
}
