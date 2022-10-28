<table width="100%" class="text-sm">
    <thead>
        <tr>
            <th class="text-left" width="5%">#</th>
            @if ($invoice->tax_per_item)
                <th class="text-left" width="30%">{{ __('messages.product') }}</th>
            @else
                <th class="text-left" width="40%">{{ __('messages.product') }}</th>
            @endif
            <th class="text-center">{{ __('messages.quantity') }}</th>
            <th class="text-center">{{ __('messages.price') }}</th>
            @if ($invoice->tax_per_item)
                <th class="text-left" width="20%">{{ __('messages.tax') }}</th>
            @endif
            @if ($invoice->discount_per_item)
                <th class="text-left">{{ __('messages.discount') }}</th>
            @endif
            <th class="text-right">{{ __('messages.amount') }}</th>
        </tr>
    </thead>
    <tbody class="invoice-list">
        @foreach ($invoice->items as $item)
            <tr>
                <td>
                    {{ $loop->iteration }}
                </td>
                <td>
                    <span>{{ $item->product->name }}</span><br>
                    <span class="text-xs text-color-gray">{!! nl2br(htmlspecialchars($item->product->description)) !!}</span>
                </td>
                <td class="text-center">
                    {{ $item->quantity }}
                </td>
                <td class="text-center">
                    {!! money($item->price, $invoice->currency_code)->format() !!}
                </td>

                @if ($invoice->tax_per_item)
                    <td>
                        @foreach ($item->getTotalPercentageOfTaxesWithNames() as $key => $value)
                            {{ $key . ' (' . $value . '%' . ')' }} <br>
                        @endforeach
                    </td>
                @endif

                @if ($invoice->discount_per_item)
                    <td>
                        {{ $item->discount_val }}%
                    </td>
                @endif

                <td class="text-right">
                    {!! money($item->total, $invoice->currency_code)->format() !!}
                </td>
            </tr>
            @if($loop->iteration % 20 == 0) 
                <tr>
                    <td>
                        <div class="page-break"></div> 
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
    <tfoot class="border-top">
        <tr>
            @php
                $colspan = 4;
                if ($invoice->tax_per_item) {
                    $colspan += 1;
                }
                if ($invoice->discount_per_item) {
                    $colspan += 1;
                }
            @endphp
            <td colspan="{{ $colspan }}"></td>
            <td align="right">
                <table class="text-right sumtable" width="100%">
                    <tr>
                        <td>
                            <h4 class="text-xs text-left text-color-gray m-10">{{ __('messages.sub_total') }}</h4>
                        </td>
                        <td>
                            <p class="text-xs text-color-gray m-10">
                                @if ($invoice->tax_per_item == false)
                                    {!! money($invoice->sub_total, $invoice->currency_code)->format() !!}
                                @else
                                    {!! money($invoice->getItemsSubTotalByBasePrice(), $invoice->currency_code)->format() !!}
                                @endif
                            </p>
                        </td>
                    </tr>
            
                    @if ($invoice->tax_per_item == false)
                        @foreach ($invoice->getTotalPercentageOfTaxesWithNames() as $key => $value)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ $key . ' (' . $value . '%' . ')' }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        {!! money(($value / 100) * $invoice->sub_total, $invoice->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($invoice->getItemsTotalPercentageOfTaxesWithNames() as $key => $value)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ $key }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        {!! money($value, $invoice->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @endif
            
                    @if ($invoice->discount_per_item == false)
                        @if ($invoice->discount_val > 0)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ __('messages.discount') . ' (' . $invoice->discount_val . '%)' }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money(($invoice->discount_val / 100) * $invoice->sub_total, $invoice->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endif
                    @else
                        @php $discount_val = $invoice->getItemsTotalDiscount() @endphp
                        @if ($discount_val > 0)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ __('messages.discount') }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money($discount_val, $invoice->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endif
                    @endif
            
                    @if (get_company_setting('invoice_show_payments_on_pdf', $invoice->id))
                        @if (count($invoice->payments) > 0)
                            @foreach ($invoice->payments as $payment)
                                @if ($payment->credit_note_id)
                                    @continue
                                @endif
                                <tr>
                                    <td>
                                        <h4 class="text-xs text-left text-color-gray m-10">{{ $payment->payment_number }}</h4>
                                    </td>
                                    <td>
                                        <p class="text-xs text-color-gray m-10">
                                            - {!! money($payment->amount, $payment->currency_code)->format() !!}
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endif
            
                    @if (count($invoice->credits) > 0)
                        @foreach ($invoice->credits as $credit)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ $credit->payment_method->name ?? '-' }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money($credit->amount, $credit->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    
                    <tr>
                        <td class="border-top">
                            <h3 class="text-sm text-dark text-left m-10">{{ __('messages.total') }}</h3>
                        </td>
                        <td class="border-top">
                            <h3 class="text-sm text-dark m-10">{!! money($invoice->total, $invoice->currency_code)->format() !!}</h3>
                        </td>
                    </tr>

                    @if (count($invoice->payments) > 0)
                        <tr>
                            <td>
                                <h3 class="text-sm text-dark text-left m-10">{{ __('messages.due_amount') }}</h3>
                            </td>
                            <td>
                                <h3 class="text-sm text-dark m-10">{!! money($invoice->due_amount, $invoice->currency_code)->format() !!}</h3>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </tfoot>
</table>
