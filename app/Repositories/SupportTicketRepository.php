<?php

namespace App\Repositories;

use App\Interfaces\SupportTicketInterface;
use App\Models\Client;
use App\Models\SupportTicket;
use Illuminate\Support\Arr;

final class SupportTicketRepository implements SupportTicketInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $model;

    public function __construct()
    {
        $this->model = SupportTicket::query();
    }

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return null|SupportTicket
     */
    public function storeSupportTicket(Client $client, array $data): ?SupportTicket
    {
        /** @var SupportTicket $supportTicket */
        $supportTicket = $this->model->create(
            [
                'creator_email' => $client->email,
                'creator_name' => $client->name,
                'subject' => Arr::get($data, 'ticket.subject'),
                'receive_notifications' => Arr::get($data, 'ticket.receive_notifications'),
                'category' => Arr::get($data, 'ticket.category'),
            ]
        );

        if (!$supportTicket) {
            return null;
        }

        $supportTicket->messages()
            ->create($data['message']);

        return $supportTicket;
    }

    /**
     * @param string      $email
     * @param bool        $includeClosed
     * @param null|string $category
     * @param null|string $subject
     * @param null|array  $createDate
     * @param null|array  $lastUpdate
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredSupportTicketsOfUser(
        string $email,
        bool $includeClosed,
        ?string $category,
        ?string $subject,
        ?array $createDate,
        ?array $lastUpdate
    ) {
        $query = $this->model->newQuery();
        $query->where('creator_email', $email);

        if (!$includeClosed) {
            $query->whereNotNull('closed_at');
        }

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($subject)) {
            $query->where('subject', 'LIKE', '%' . $subject . '%');
        }

        if (is_array($createDate)) {
            $query->whereBetween('created_at', $createDate);
        }

        if (is_array($lastUpdate)) {
            $query->whereBetween('updated_at', $lastUpdate);
        }

        return $query->get();
    }
}
