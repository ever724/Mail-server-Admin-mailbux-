<?php

namespace App\Models;

use App\Traits\HasAddresses;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Paddle\Billable;
use Spatie\Permission\Traits\HasRoles;

class Client extends Model
{
    use Notifiable;
    use UUIDTrait;
    use HasRoles;
    use HasAddresses;
    use Billable;

    protected $fillable = [
        'id',
        'uid',
        'name',
        'organization',
        'email',
        'last_synced_at',
        'exists_on_mail_server',
        'recovery_email',
        'api_access',
        'enabled',
        'domain',
        'language',
        'last_login',
        'storagequota_total',
        'storagequota_used',
        'deleted_at',
    ];

    protected $dates = [
        'last_synced_at',
        'last_login',
    ];

    public function getStorageUsagePercentageAttribute(): ?int
    {
        if ($this->storagequota_total < 1) {
            return null;
        }

        $used = $this->storagequota_used;
        $total = $this->storagequota_total;

        return round(($used * 100 / $total));
    }

    public function plan_subscriptions(): HasMany
    {
        return $this->hasMany(PlanSubscription::class);
    }
}
