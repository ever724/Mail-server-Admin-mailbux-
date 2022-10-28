<?php

namespace App\Models;

use App\External\MailbuxServer\References;
use App\Jobs\CreateDomainAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainAdmin extends Model
{
    protected $fillable = [
        'subscription_id',
        'username',
        'password',
        'api_access',
        'enabled',
        'recovery_email',
        'language',
        'domains',
        'storagequota_total',
        'quota_domains',
        'quota_mailboxes',
        'quota_aliases',
        'quota_domainaliases',
    ];

    protected $hidden = [
        'password',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(PlanSubscription::class);
    }

    public function getDomainsAttribute(string $value)
    {
        return explode(', ', $value);
    }

    public function setDomainsAttribute(array $domains)
    {
        $this->attributes['domains'] = implode(', ', $domains);
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $domainAdmin) {
            $client = $domainAdmin->subscription->client;

            if (!isset($domainAdmin->recovery_email)) {
                $domainAdmin->recovery_email = $client->recovery_email ?? $client->email ?? '';
            }

            dispatch_now(
                new CreateDomainAdmin(
                    $domainAdmin->username,
                    $domainAdmin->password,
                    $domainAdmin->recovery_email,
                    References::PERMISSION_LEVEL_DOMAIN_ADMIN,
                    true,
                    true,
                    $domainAdmin->domains ?? [],
                    $domainAdmin->storagequota_total ?? 0,
                    $domainAdmin->quota_domains ?? 0,
                    $domainAdmin->quota_mailboxes ?? 0,
                    $domainAdmin->quota_aliases ?? 0,
                    $domainAdmin->quota_domainaliases ?? 0
                )
            );
        });
    }
}
