@if($invoices->count() > 0)
    <div class="table-responsive">
        <table class="table mb-0 thead-border-top-0 table-striped">
            <thead>
            <tr>
                <th class="w-30px text-center">{{ __('messages.#id') }}</th>
                <th class="text-center">{{ __('messages.order_number') }}</th>
                <th class="text-center">{{ __('messages.client_name') }}</th>
                <th class="text-center">{{ __('messages.period') }}</th>
                <th class="text-center">{{ __('messages.status') }}</th>
                <th class="text-center">{{ __('messages.payment_method') }}</th>
                <th class="text-center">{{ __('messages.amount') }}</th>
                <th class="text-center">{{ __('messages.next_payment_date') }}</th>
                <th class="w-50px text-center"></th>
            </tr>
            </thead>
            <tbody class="list" id="support_tickets">
            @foreach ($invoices as $invoice)
                <tr>
                    <td class="text-center">{{$invoice->id}}</td>
                    <td class="text-center">{{$invoice->order_number}}</td>
                    <td class="text-center">{{$invoice->subscription->client->name ?? ''}}</td>
                    <td class="text-center">{{is_null($invoice->paid_at) ? "-" : $invoice->paid_at->format('m/Y')}}</td>
                    <td class="text-center">{!!__('messages.invoice_statuses.'.$invoice->status) !!}</td>
                    <td class="text-center">{!! __('messages.invoice_payment_method.'.$invoice->payment_method) !!}</td>
                    <td class="text-center">${{number_format($invoice->amount)}}</td>
                    <td class="text-center">{{$invoice->next_payment_date->format('Y-m-d')}}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{route('payment.invoices.html', [$invoice->client, $invoice])}}"
                               class="btn btn-secondary btn-sm">
                                View
                            </a>
                            <a href="{{route('payment.invoices.pdf', [$invoice->client, $invoice])}}"
                               class="btn btn-primary btn-sm">
                                PDF
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row card-body pagination-light justify-content-center text-center">
        {{ $invoices->links() }}
    </div>
@else
    <div class="row justify-content-center card-body pb-0 pt-5">
        <i class="material-icons fs-64px">account_box</i>
    </div>
    <div class="row justify-content-center card-body pb-5">
        <p class="h4">{{ __('messages.no_invoices') }}</p>
    </div>
@endif