<?php

namespace App\Models;

use App\Traits\CompanyUserTrait;
use App\Traits\HasAddresses;
use App\Traits\UUIDTrait;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use UUIDTrait;
    use HasRoles;
    use HasAddresses;
    use CompanyUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'telephone',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Define Relation with UserSetting Model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    /**
     * Get User Specified setting.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getSetting($key)
    {
        return UserSetting::getSetting($key, $this->id);
    }

    /**
     * Set User Specified setting.
     *
     * @param string $key
     * @param string $value
     */
    public function setSetting($key, $value)
    {
        return UserSetting::setSetting($key, $value, $this->id);
    }

    /**
     * Get Full Name Attribute.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Return Default User Avatar Url.
     *
     * @return string (url)
     */
    public function getDefaultAvatar()
    {
        return asset('assets/images/avatar/user.png');
    }

    /**
     * Get User's Avatar Url || Default Avatar.
     *
     * @return string (url)
     */
    public function getAvatarAttribute()
    {
        $avatar = $this->getSetting('avatar');

        return $avatar ? asset($avatar) : $this->getDefaultAvatar();
    }

    /**
     * Send email verification email.
     */
    public function sendEmailVerificationNotification()
    {
        try {
            $this->notify(new VerifyEmail);
        } catch (\Throwable $th) {
        }
    }

    /**
     * Determine if the model may perform the given permission.
     *
     * @param int|\Spatie\Permission\Contracts\Permission|string $permission
     * @param null|string                                        $guardName
     *
     * @throws PermissionDoesNotExist
     *
     * @return bool
     */
    public function hasPermission($permission, $guardName = null): bool
    {
        return $this->id ? $this->hasPermissionTo($permission, $guardName) : 1;
    }

    /**
     * Update current user's settings.
     *
     * @param mixed $request
     */
    public function updateModel($request)
    {
        // Update User
        $validated = $request->validated();
        unset($validated['password']);
        $this->update($validated);

        // If Password fields are filled
        if ($request->old_password && $request->new_password) {
            $this->password = Hash::make($request->new_password);
            $this->save();
        }

        // Upload and save avatar
        if ($request->avatar) {
            $request->validate(['avatar' => 'required|image|mimes:png,jpg|max:2048']);
            $path = $request->avatar->storeAs('avatars', 'avatar-' . $this->id . '.' . $request->avatar->getClientOriginalExtension(), 'public_dir');
            $this->setSetting('avatar', asset('/uploads/' . $path));
        }

        return $this;
    }
}
