<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment</title>
    @include('layouts._favicons')
    @paddleJS
</head>

<body class="layout-default has-drawer-opened">
    <div class="checkout-container">        
    </div>
    <!-- Application Scripts -->
    <script type="text/javascript">
            Paddle.Checkout.open({
                override: "{{$payLink}}",
                method: 'inline',
                allowQuantity: false,
                disableLogout: true,
                frameTarget: 'checkout-container', // The className of your checkout <div>
                frameInitialHeight: 416,
                frameStyle: 'width:100%; min-width:312px; background-color: transparent; border: none;' // Please ensure the minimum width is kept at or above 286px with checkout padding disabled, or 312px with checkout padding enabled. See "General" section under "Branded Inline Checkout" below for more information on checkout padding.
            });        
    </script>    
</body>
</html>