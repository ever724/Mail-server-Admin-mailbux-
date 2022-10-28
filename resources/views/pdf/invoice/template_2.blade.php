<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @include('pdf._style')
    <style>
        thead th {
            color: {{$invoice->company->getSetting('invoice_color')}};
        }
    </style>
</head>
<body>
    <div class="information" style="background: {{$invoice->company->getSetting('invoice_color')}};border:unset;">
        <table class="text-sm" width="100%">
            <tr>
                <td align="left" style="width: 40%;">
                    @if(get_company_setting('avatar', $invoice->company->id))
                        <img height="30" src="{{ $invoice->company->avatar }}" alt="{{ $invoice->company->name }}">
                    @else
                        <h1 class="m-0 text-white">{{$invoice->company->name}}</h1>
                    @endif
                </td>
                <td align="right" style="width: 40%;">
                    <h1 class="m-1 text-white">{{ __('messages.invoice') }}</h1>
                    <h4 class="m-1 text-white">{{$invoice->invoice_number}}</h4>
                    <h4 class="m-1 text-white">{{ __('messages.invoice_date') }}: {{$invoice->formatted_invoice_date}}</h4>
                    <h4 class="m-1 text-white">{{ __('messages.due_date') }}: {{$invoice->formatted_due_date}}</h4>
                    @if ($invoice->reference_number)
                        <h4 class="m-1 text-white">{{ __('messages.reference_number') }}: {{$invoice->reference_number}}</h4>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice">
        <div class="grid">
            <div class="col">
                <h3 class="m-0 text-color-gray">{{ __('messages.from') }}</h3>
                <span class="address text-sm">
                    {!! $invoice->getField('invoice_from_template') !!}
                </span>
            </div>
            <div class="col">
                <h3 class="m-0 text-color-gray">{{ __('messages.to') }}</h3>
                <span class="address text-sm">
                    {!! $invoice->getField('invoice_to_template') !!}
                </span>
            </div>
            <div class="col">
                @if($invoice->customer->shipping->address_1)
                    <h3 class="m-0 text-color-gray">{{ __('messages.ship_to') }}</h3>
                    <span class="address text-sm">
                        {!! $invoice->getField('invoice_ships_to_template') !!}
                    </span>
                @endif
            </div>
        </div>

        <div class="grid">
            <div class="col">
                @include('pdf.invoice._table')       
            </div>
        </div>

        @if($invoice->notes)
            <div class="grid">
                <div class="col text-xs">
                    <h3 class="m-0 text-color-gray">{{ __('messages.notes') }}</h3>
                    {{ $invoice->notes }}
                </div>
            </div>
        @endif
    </div>

    <div class="information" style="position: absolute; bottom: 0;">
        <div class="grid">
            <div class="col address text-xs m-0">
                {!! $invoice->company->getSetting('invoice_footer') !!}
            </div>
        </div>
        <table width="100%" class="text-sm border-top">
            @if($invoice->company->subscription('main')->getFeatureValue('advertisement_on_mails') === '1')
                <tr>
                    <td class="text-xs" align="left" style="width: 50%;">
                        {{ __('messages.pdf_footer_left', ['app_name' => get_system_setting('application_name')]) }}
                    </td>
                    <td class="text-xs" align="right" style="width: 50%;">
                        {{ route('home') }}
                    </td>
                </tr>
            @endif
        </table>
    </div>
</body>
</html>

