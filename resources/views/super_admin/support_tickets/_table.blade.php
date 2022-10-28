@if($supportTickets->count() > 0)
    <div class="table-responsive">
        <table class="table mb-0 thead-border-top-0 table-striped">
            <thead>
            <tr>
                <th class="w-30px" class="text-center">{{ __('messages.#id') }}</th>
                <th>{{ __('messages.sender_name') }}</th>
                <th>{{ __('messages.sender_email') }}</th>
                <th>{{ __('messages.subject') }}</th>
                <th>{{ __('messages.category') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th class="text-center width: 120px;">{{ __('messages.created_at') }}</th>
                <th class="text-center width: 120px;">{{ __('messages.last_update') }}</th>
                <th class="w-50px"></th>
            </tr>
            </thead>
            <tbody class="list" id="support_tickets">
            @foreach ($supportTickets as $ticket)
                <tr @if($ticket->unread_messages > 0) class="table-dark-gray unread" data-id="{{$ticket->id}}" @endif>
                    <td>
                        <a class="badge"
                           href="{{route('super_admin.support_tickets.show', [$ticket])}}">#{{ $ticket->id }}</a>
                    </td>
                    <td>
                        <p>{{ $ticket->first_message->sender_name }}</p>
                    </td>
                    <td>
                        <p>{{ $ticket->first_message->sender_email }}</p>
                    </td>
                    <td>
                        <p>{{ $ticket->subject }}</p>
                    </td>
                    <td>
                        <p>{{ $ticket->category }}</p>
                    </td>
                    <td>
                        <p class="ticket-status">{!! __('messages.support_ticket_status.'. $ticket->status) !!}</p>
                    </td>
                    <td class="text-center">
                        <i class="material-icons icon-16pt text-muted-light mr-1">today</i> {{ $ticket->created_at->diffForHumans() }}
                    </td>
                    <td class="text-center">
                        <i class="material-icons icon-16pt text-muted-light mr-1">today</i> {{ $ticket->updated_at->diffForHumans() }}
                    </td>
                    <td>
                        <a type="button" class="btn btn-primary" href="{{route('super_admin.support_tickets.show', $ticket)}}">View</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row card-body pagination-light justify-content-center text-center">
        {{ $supportTickets->links() }}
    </div>
@else
    <div class="row justify-content-center card-body pb-0 pt-5">
        <i class="material-icons fs-64px">account_box</i>
    </div>
    <div class="row justify-content-center card-body pb-5">
        <p class="h4">{{ __('messages.no_support_tickets') }}</p>
    </div>
@endif