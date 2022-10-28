<table width="100%" class="text-sm">
    <thead>
        <tr>
            <th class="text-left" width="5%">#</th>
            @if ($estimate->tax_per_item)
                <th class="text-left" width="30%">{{ __('messages.product') }}</th>
            @else
                <th class="text-left" width="40%">{{ __('messages.product') }}</th>
            @endif
            <th class="text-center">{{ __('messages.quantity') }}</th>
            <th class="text-center">{{ __('messages.price') }}</th>
            @if ($estimate->tax_per_item)
                <th class="text-left" width="20%">{{ __('messages.tax') }}</th>
            @endif
            @if ($estimate->discount_per_item)
                <th class="text-left">{{ __('messages.discount') }}</th>
            @endif
            <th class="text-right">{{ __('messages.amount') }}</th>
        </tr>
    </thead>
    <tbody class="estimate-list">
        @foreach ($estimate->items as $item)
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
                    {!! money($item->price, $estimate->currency_code)->format() !!}
                </td>

                @if ($estimate->tax_per_item)
                    <td>
                        @foreach ($item->getTotalPercentageOfTaxesWithNames() as $key => $value)
                            {{ $key . ' (' . $value . '%' . ')' }} <br>
                        @endforeach
                    </td>
                @endif

                @if ($estimate->discount_per_item)
                    <td>
                        {{ $item->discount_val }}%
                    </td>
                @endif

                <td class="text-right">
                    {!! money($item->total, $estimate->currency_code)->format() !!}
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
                if ($estimate->tax_per_item) {
                    $colspan += 1;
                }
                if ($estimate->discount_per_item) {
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
                                @if ($estimate->tax_per_item == false)
                                    {!! money($estimate->sub_total, $estimate->currency_code)->format() !!}
                                @else
                                    {!! money($estimate->getItemsSubTotalByBasePrice(), $estimate->currency_code)->format() !!}
                                @endif
                            </p>
                        </td>
                    </tr>
            
                    @if ($estimate->tax_per_item == false)
                        @foreach ($estimate->getTotalPercentageOfTaxesWithNames() as $key => $value)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ $key . ' (' . $value . '%' . ')' }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        {!! money(($value / 100) * $estimate->sub_total, $estimate->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($estimate->getItemsTotalPercentageOfTaxesWithNames() as $key => $value)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ $key }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        {!! money($value, $estimate->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @endif
            
                    @if ($estimate->discount_per_item == false)
                        @if ($estimate->discount_val > 0)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ __('messages.discount') . ' (' . $estimate->discount_val . '%)' }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money(($estimate->discount_val / 100) * $estimate->sub_total, $estimate->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endif
                    @else
                        @php $discount_val = $estimate->getItemsTotalDiscount() @endphp
                        @if ($discount_val > 0)
                            <tr>
                                <td>
                                    <h4 class="text-xs text-left text-color-gray m-10">{{ __('messages.discount') }}</h4>
                                </td>
                                <td>
                                    <p class="text-xs text-color-gray m-10">
                                        - {!! money($discount_val, $estimate->currency_code)->format() !!}
                                    </p>
                                </td>
                            </tr>
                        @endif
                    @endif
                    
                    <tr>
                        <td class="border-top">
                            <h3 class="text-sm text-dark text-left m-10">{{ __('messages.total') }}</h3>
                        </td>
                        <td class="border-top">
                            <h3 class="text-sm text-dark m-10">{!! money($estimate->total, $estimate->currency_code)->format() !!}</h3>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tfoot>
</table>
