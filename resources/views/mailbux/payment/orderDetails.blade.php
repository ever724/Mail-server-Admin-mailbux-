<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @include('layouts._favicons')
    @include('layouts._css')
</head>
<body class="layout-default has-drawer-opened">
    <div class="mdk-header-layout js-mdk-header-layout">
        <div class="mdk-header-layout__content pt-64px">
            <div class="mdk-drawer-layout js-mdk-drawer-layout">
                <div class="mdk-drawer-layout__content page">
                    <div class="container-fluid page__container">
                    <table class="table">
                        @if(isset($orderDetail))
                        @foreach($orderDetail as $key => $value)
                            <tr>
                                <td>{{$key}}</td>
                                @if(is_array($value))
                                <td><table>
                                    @foreach($value as $k => $v)
                                    @if(!is_array($v))
                                    <tr>
                                        <td>{{$k}}</td>
                                        <td>{{$v}}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </table></td>
                                @else
                                <td>{{$value}}</td>
                                @endif
                            </tr>
                        @endforeach
                        @else
                        <tr><td>You recent payment was failed with payment number. Please try again.</td></tr>
                        @endif
                    </table>
                    </div>
                </div>               
            </div>
        </div>
    </div>   
    @include('layouts._js')
    @include('layouts._flash')
</body>
</html>