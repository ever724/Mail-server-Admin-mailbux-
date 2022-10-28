<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estimate</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @include('pdf._style')
</head>
<body>
    <div class="information border-bottom">
        <table class="text-sm" width="100%">
            <tr>
                <td align="left" style="width: 40%;">
                    @if(get_company_setting('avatar', $estimate->company->id))
                        <img height="30" src="{{ $estimate->company->avatar }}" alt="{{ $estimate->company->name }}">
                    @else
                        <h1 class="m-0" style="color: {{$estimate->company->getSetting('estimate_color')}}">{{$estimate->company->name}}</h1>
                    @endif
                </td>
                <td align="right" style="width: 40%;">
                    <h3 class="m-0 text-color-gray">{{ __('messages.from') }}</h3>
                    <span class="address address-margin text-sm">
                        {!! $estimate->getField('estimate_from_template') !!}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="estimate">
        <div class="grid">
            <div class="col">
                <h3 class="m-0 text-color-gray">{{ __('messages.to') }}</h3>
                <span class="address text-sm">
                    {!! $estimate->getField('estimate_to_template') !!}
                </span>
            </div>
            <div class="col">
                @if($estimate->customer->shipping->address_1)
                    <h3 class="m-0 text-color-gray">{{ __('messages.ship_to') }}</h3>
                    <span class="address text-sm">
                        {!! $estimate->getField('estimate_ships_to_template') !!}
                    </span>
                @endif
            </div>
            <div class="col text-right">
                <h4>{{ __('messages.estimate') }}</h4>
                <h5 class="text-sm">{{ __('messages.estimate_number') }}: <span class="fw-normal">{{$estimate->estimate_number}}</span></h5>
                <h5 class="text-sm">{{ __('messages.estimate_date') }}: <span class="fw-normal">{{$estimate->formatted_estimate_date}}</span></h5>
                <h5 class="text-sm">{{ __('messages.due_date') }}: <span class="fw-normal">{{$estimate->formatted_due_date}}</span></h5>
                @if ($estimate->reference_number)
                    <h5 class="text-sm">{{ __('messages.reference_number') }}: {{$estimate->reference_number}}</h5>
                @endif
            </div>
        </div>

        <br />

        <div class="grid">
            <div class="col">
                @include('pdf.estimate._table')       
            </div>
        </div>

        @if($estimate->notes)
            <div class="grid">
                <div class="col text-xs">
                    <h3 class="m-0 text-color-gray">{{ __('messages.notes') }}</h3>
                    {{ $estimate->notes }}
                </div>
            </div>
        @endif
    </div>

    <div class="information" style="position: absolute; bottom: 0;">
        <div class="grid">
            <div class="col address text-xs m-0">
                {!! $estimate->company->getSetting('estimate_footer') !!}
            </div>
        </div>
        <table width="100%" class="text-sm border-top">
            @if($estimate->company->subscription('main')->getFeatureValue('advertisement_on_mails') === '1')
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

