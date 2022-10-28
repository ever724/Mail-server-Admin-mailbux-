<table width="100%" class="text-sm">
    <thead>
        <tr>
            <th class="text-left" width="5%">#</th>
            @if ($credit_note->tax_per_item)
                <th class="text-left" width="30%">{{ __('messages.product') }}</th>
            @else
                <th class="text-left" width="40%">{{ __('messages.product') }}</th>
            @endif
            <th class="text-center">{{ __('messages.quantity') }}</th>
            <th class="text-center">{{ __('messages.price') }}</th>
            @if ($credit_note->tax_per_item)
                <th class="text-left" width="20%">{{ __('messages.tax') }}</th>
            @endif
            @if ($credit_note->discount_per_item)
                <th class="text-left">{{ __('messages.discount') }}</th>
            @endif
            <th class="text-right">{{ __('messages.amount') }}</th>
        </tr>
    </thead>
    <tbody class="credit_note-list">
        @foreach ($credit_note->items as $item)
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
                    {!! money($item->price, $credit_note->currency_code)->format() !!}
                </td>

                @if ($credit_note->tax_per_item)
                    <td>
                        @foreach ($item->getTotalPercentageOfTaxesWithNames() as $key => $value)
                            {{ $key . ' (' . $value . '%' . ')' }} <br>
                        @endforeach
                    </td>
                @endif

                @if ($credit_note->discount_per_item)
                    <td>
                        {{ $item->discount_val }}%
                    </td>
                @endif

                <td class="text-right">
                    {!! money($item->total, $credit_note->currency_code)->format() !!}
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
                if ($credit_note->tax_per_item) {
                    $colspan += 1;
                }
                if ($credit_note->discount_per_item) {
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
                                @if ($credit_note->tax_per_item == false)
                                    {!! money($credit_note->sub_total, $credit_note->currency_code)->format() !!}
                                @else
                                    {!! money($credit_note->getItemsSubTotalByBasePrice(), $credit_note->currency_code)->format() !!}
                                @endif
                            </p>
                        </td>
                    </tr>
            
                    @if ($credit_note->tax_per_item == false)
                        @foreach ($credit_note->getTotalPercentageOfTaxesWithNames() as $key => $value)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ $key . ' (' . $value . '%' . ')' }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        {!! money(($value / 100) * $credit_note->sub_total, $credit_note->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($credit_note->getItemsTotalPercentageOfTaxesWithNames() as $key => $value)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ $key }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        {!! money($value, $credit_note->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @endif
            
                    @if ($credit_note->discount_per_item == false)
                        @if ($credit_note->discount_val > 0)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ __('messages.discount') . ' (' . $credit_note->discount_val . '%)' }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money(($credit_note->discount_val / 100) * $credit_note->sub_total, $credit_note->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endif
                    @else
                        @php $discount_val = $credit_note->getItemsTotalDiscount() @endphp
                        @if ($discount_val > 0)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ __('messages.discount') }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money($discount_val, $credit_note->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endif
                    @endif
                    
                    <tr>
                        <td class="border-top">
                            <h3 class="text-xs text-left text-color-gray m-10">{{ __('messages.total') }}</h3>
                        </td>
                        <td class="border-top">
                            <p class="text-xs text-color-gray m-10">{!! money($credit_note->total, $credit_note->currency_code)->format() !!}</p>
                        </td>
                    </tr>

                    @if (count($credit_note->applied_payments) > 0)
                        @foreach ($credit_note->applied_payments as $payment)
                            <tr>
                                <td class="border-top">
                                    <h3 class="text-xs text-left text-color-gray m-10">{{ $payment->invoice->display_name }}</h3>
                                </td>
                                <td class="border-top">
                                    <p class="text-xs text-color-gray m-10">- {!! money($payment->amount, $payment->currency_code)->format() !!}</p>
                                </td>
                            </tr>
                        @endforeach
                    @endif
            
                    @if (count($credit_note->refunds) > 0)
                        @foreach ($credit_note->refunds as $refund)
                            <tr>
                                <td class="border-top">
                                    <h3 class="text-xs text-left text-color-gray m-10">{{ __('messages.refund') }}</h3>
                                </td>
                                <td class="border-top">
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money($refund->amount, $refund->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @endif
            
                    <tr>
                        <td class="border-top">
                            <h3 class="text-sm text-left text-dark m-10">{{ __('messages.remaining_balance') }}</h3>
                        </td>
                        <td class="border-top">
                            <p class="text-xs text-dark m-10">{!! money($credit_note->remaining_balance, $credit_note->currency_code)->format() !!}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tfoot>
</table>

