<?php

namespace App\Interfaces;

use App\Models\Client;
use App\Models\SupportTicket;

interface SupportTicketInterface
{
    /**
     * @param Client $client
     * @param array  $data
     *
     * @return null|SupportTicket
     */
    public function storeSupportTicket(Client $client, array $data): ?SupportTicket;

    /**
     * @param string      $email
     * @param bool        $includeClosed
     * @param null|string $category
     * @param null|string $subject
     * @param array       $createDate
     * @param array       $lastUpdate
     *
     * @return mixed
     */
    public function getFilteredSupportTicketsOfUser(
        string $email,
        bool $includeClosed,
        ?string $category,
        ?string $subject,
        array $createDate,
        array $lastUpdate
    );
}
