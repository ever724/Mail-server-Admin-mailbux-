<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Expenses Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
            color: #040405;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: large;
        }

        tfoot tr {
            padding: 10px;
        }

        .invoice table {
            margin: 15px;
        }

        .invoice h3 {
            margin-left: 15px;
        }

        .information {
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
        }

        .information h2 {
            text-align: center;
            font-size: large;
        }

        .information h3 {
            font-weight: normal;
        }

        .information .logo {
            margin: 5px;
        }

        .information table {
            padding: 10px;
        }

        .heading {
            color: #595959;
            margin: 0px;
        }

        .text-sm {
            font-size: x-small;
        }

        .text-center {
            text-align: center;
        }

        .border-top {
            border-top: 1px solid #dddddd;
        }
    </style>
</head>

<body>
    <div class="information">
        <table class="text-sm" width="100%">
            <tr>
                <td align="left" style="width: 40%;">
                    @if(get_company_setting('avatar', $company->id)) 
                        <img height="30" src="{{ $company->avatar }}" alt="{{ $company->name }}">
                    @else
                        <h1>{{ $company->name }}</h1>
                    @endif
                </td>
                <td align="right" style="width: 40%;">
                    <h3>{{ $from->format($company->getSetting('date_format')) }} - {{ $to->format($company->getSetting('date_format')) }}</h3>
                </td>
            </tr>
        </table>
    </div>

    <br />

    <div class="invoice">
        <h2 class="heading text-center">{{ __('messages.vendor_report') }}</h2>

        <h3>{{ __('messages.vendors') }}</h3>
        <table width="100%">
            <tbody>
                @foreach ($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->display_name }}</td>
                        <td colspan="2"></td>
                        <td align="right">{!! money($vendor->total_expense, $company->currency->short_code) !!}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-top">
                <tr>
                    <td colspan="3"></td>
                    <td align="right">
                        {{ __('messages.total_expense') }}: {!! money($total_loss, $company->currency->short_code)->format() !!}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="information" style="position: absolute; bottom: 0;">
        <table width="100%" class="text-sm">
            @if($company->subscription('main')->getFeatureValue('advertisement_on_mails') === '1')
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
