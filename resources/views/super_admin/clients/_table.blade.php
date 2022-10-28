@if($clients->count() > 0)
    <div class="table-responsive">
        <table class="table mb-0 thead-border-top-0 table-striped">
            <thead>
            <tr>
                <th>{{__('messages.client.id')}}</th>
                <th>{{__('messages.client.name')}}</th>
                <th>{{__('messages.client.organization')}}</th>
                <th>{{__('messages.client.email')}}</th>
                <th>{{__('messages.client.last_login')}}</th>
                <th>{{__('messages.client.storage_used')}}</th>
                <th class="text-center">{{__('messages.client.exists_on_mail_server')}}</th>
                <th></th>
            </tr>
            </thead>
            <tbody class="list" id="clients">
            @foreach ($clients as $client)
                <tr>
                    <td>{{$client->id}}</td>
                    <td>{{$client->name}}</td>
                    <td>{{$client->organization}}</td>
                    <td>{{$client->email}}</td>
                    <td>{{$client->last_login}}</td>
                    <td>
                        @if($client->storage_usage_percentage !== null)
                            <div class="text text-center text-grey">
                                {{$client->storage_usage_percentage}}%
                            </div>
                            <div class="progress" data-toggle="tooltip" data-placement="top"
                                 title="{{$client->storagequota_used}} / {{$client->storagequota_total}} MB">
                                <div
                                        class="progress-bar
                        @if($client->storage_usage_percentage < 50) bg-success
                        @elseif($client->storage_usage_percentage < 90) bg-warning
                        @else bg-danger @endif" style="width:{{$client->storage_usage_percentage}}%">
                                </div>
                            </div>
                        @else
                            <div class="text text-black text-center">
                                No data
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        {!! __('messages.bool-badge.'.$client->exists_on_mail_server) !!}
                    </td>
                    <td>
                        <div class="d-inline">
                            <a href="{{route('super_admin.clients.delete', [$client])}}"
                               class="btn btn-sm btn-danger delete-confirm">
                                <i class="fas fa-trash"></i>
                            </a>
                            <a href="{{route('super_admin.clients.edit', [$client])}}"
                               class="btn btn-sm btn-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row card-body pagination-light justify-content-center text-center">
        {{ $clients->links() }}
    </div>
    <div class="card-footer">
        {{__('messages.displaying_records', [$clients->firstItem(), $clients->lastItem(), $clients->total()])}}
    </div>
@else
    <div class="row justify-content-center card-body pb-0 pt-5">
        <i class="material-icons fs-64px">account_box</i>
    </div>
    <div class="row justify-content-center card-body pb-5">
        <p class="h4">{{ __('messages.no_clients') }}</p>
    </div>
@endif