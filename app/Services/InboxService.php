<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InboxService
{
    const DB_CONNECTION = 'inbox';

    /**
     * @var Builder
     */
    private $userRepository;

    /**
     * @var Builder
     */
    private $identitiesRepository;

    public function __construct()
    {
        $this->userRepository = DB::connection(self::DB_CONNECTION)->table('users');
        $this->identitiesRepository = DB::connection(self::DB_CONNECTION)->table('identities');
    }

    public function getAllInboxClients(): Collection
    {
        return $this->userRepository()
            ->leftJoin('identities', 'identities.user_id', '=', 'users.user_id')
            ->selectRaw('*, users.user_id as id')
            ->get();
    }

    /**
     * @param string      $username
     * @param null|string $password
     * @param string      $mailHost
     * @param string      $language
     * @param array       $preferences
     * @param null|string $name
     * @param null|string $organization
     *
     * @return int
     */
    public function storeClient(
        string $username,
        ?string $password,
        string $mailHost,
        string $language,
        array $preferences,
        ?string $name,
        ?string $organization
    ): int {
        $userId = $this->userRepository()->insertGetId([
            'username' => $username,
            'password' => $password,
            'mail_host' => $mailHost,
            'language' => $language,
            'preferences' => serialize($preferences),
            'created' => now()->format('Y-m-d'),
        ]);

        $this->identitiesRepository()->insert([
            'user_id' => $userId,
            'name' => $name ?? '',
            'email' => $username,
            'organization' => $organization ?? '',
        ]);

        return $userId;
    }

    /**
     * @param int         $userId
     * @param string      $username
     * @param null|string $password
     * @param string      $mailHost
     * @param string      $language
     * @param null|string $name
     * @param null|string $organization
     */
    public function updateClient(
        int $userId,
        string $username,
        ?string $password,
        string $mailHost,
        string $language,
        ?string $name,
        ?string $organization
    ): void {
        $this->userRepository()
            ->where(['user_id' => $userId])
            ->update([
                'password' => $password,
                'mail_host' => $mailHost,
                'language' => $language,
            ]);

        $this->identitiesRepository()->insert([
            'user_id' => $userId,
            'name' => $name ?? '',
            'email' => $username,
            'organization' => $organization ?? '',
        ]);
    }

    /**
     * @return Builder
     */
    private function userRepository(): Builder
    {
        return clone $this->userRepository;
    }

    /**
     * @return Builder
     */
    private function identitiesRepository(): Builder
    {
        return clone $this->identitiesRepository;
    }
}
