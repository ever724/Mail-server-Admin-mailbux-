<?php

namespace App\Traits;

trait UUIDTrait
{
    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating transaction.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (self::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }

    /**
     * Find by uid.
     *
     * @param mixed $uid
     */
    public static function findByUid($uid)
    {
        return self::where('uid', $uid)->first();
    }
}
